<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use GuzzleHttp\Client as GuzzleClient;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

class Create extends Action
{
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly CheckoutSession $checkoutSession,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute(): ResponseInterface|Json|ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $result->setData(['success' => false, 'message' => 'Invalid request method.']);
        }

        $token = $request->getParam('token');
        $sessionId = $request->getParam('sessionId');
        $quoteId = (int) $request->getParam('quoteId');

        if (!$token || !$sessionId) {
            return $result->setData(['success' => false, 'message' => 'Missing token or sessionId.']);
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote || !$quote->getId()) {
            return $result->setData(['success' => false, 'message' => 'Quote not found.']);
        }

        if ($quoteId && (int) $quote->getId() !== $quoteId) {
            return $result->setData(['success' => false, 'message' => 'Quote ID mismatch.']);
        }

        if ($quote->getData('yuno_session_id') !== $sessionId) {
            return $result->setData(['success' => false, 'message' => 'Session ID does not match quote context.']);
        }

        $env = $this->scopeConfig->getValue('payment/yuno_full_checkout/yuno_credentials/environment');
        $privateKeyEnc = $this->scopeConfig->getValue(
            $env === 'production'
                ? 'payment/yuno_full_checkout/yuno_credentials/production_private_secret_key'
                : 'payment/yuno_full_checkout/yuno_credentials/sandbox_private_secret_key'
        );
        $baseUrl = $this->scopeConfig->getValue(
            $env === 'production'
                ? 'payment/yuno_full_checkout/yuno_credentials/production_base_url'
                : 'payment/yuno_full_checkout/yuno_credentials/sandbox_base_url'
        );

        if (!$privateKeyEnc || !$baseUrl) {
            return $result->setData(['success' => false, 'message' => 'Private key or base URL not configured.']);
        }

        try {
            $privateKey = $this->encryptor->decrypt($privateKeyEnc);
            $client = new GuzzleClient();

            $response = $client->post($baseUrl . 'payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $privateKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'token' => $token,
                    'sessionId' => $sessionId,
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode((string) $response->getBody(), true);

            if ($statusCode !== 200 || empty($body)) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Yuno API error [%1]: %2', $statusCode, $response->getBody())
                ]);
            }

            // Salva dados no quote
            $paymentId = $body['id'] ?? null;
            $redirectUrl = $body['hosted_url'] ?? null;

            if ($paymentId) {
                $quote->getPayment()->setAdditionalInformation('yuno_payment_id', $paymentId);
                $quote->save();
            }

            return $result->setData([
                'success' => true,
                'payment_id' => $paymentId,
                'redirect' => $redirectUrl
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Yuno Payment Create Error: ' . $e->getMessage());
            return $result->setData(['success' => false, 'message' => 'Unexpected error. Please try again later.']);
        }
    }
}
