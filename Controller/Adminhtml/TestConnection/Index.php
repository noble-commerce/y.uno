<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Controller\Adminhtml\TestConnection;

use GuzzleHttp\Client;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use NobleCommerce\Yuno\Model\Config\ConfigProvider;

class Index extends Action
{
    /**
     * Index Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ConfigProvider $configProvider
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly Context $context,
        protected readonly JsonFactory $resultJsonFactory,
        protected readonly ConfigProvider $configProvider,
        protected readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute(): Json|ResultInterface|ResponseInterface
    {
        $result = $this->resultJsonFactory->create();
        $environment = $this->configProvider->getEnvironment();
        $userId = $this->configProvider->getUserId();
        $sandboxBaseUrl = $this->configProvider->getSandboxBaseUrl();
        $productionBaseUrl = $this->configProvider->getProductionBaseUrl();

        if ($environment === 'sandbox') {
            return $this->testConnection(
                $sandboxBaseUrl,
                $userId,
                $this->configProvider->getSandboxPublicApiKey(),
                $this->configProvider->getSandboxPrivateSecretKey(),
                $result
            );
        }

        if ($environment === 'production') {
            return $this->testConnection(
                $productionBaseUrl,
                $userId,
                $this->configProvider->getProductionPublicApiKey(),
                $this->configProvider->getProductionPrivateSecretKey(),
                $result
            );
        }

        return $result->setData([
            'success' => false,
            'message' => __('Invalid environment configuration.')
        ]);
    }

    /**
     * TestConnection
     *
     * @param string $baseUrl
     * @param string|null $userId
     * @param string|null $publicKey
     * @param string|null $privateKey
     * @param $result
     * @return mixed
     */
    private function testConnection(
        string $baseUrl,
        ?string $userId,
        ?string $publicKey,
        ?string $privateKey,
        $result
    ): mixed
    {
        if (!$publicKey || !$privateKey) {
            return $result->setData([
                'success' => false,
                'message' => __('Yuno credentials are not configured.')
            ]);
        }

        $privateKeyDecrypted = $this->encryptor->decrypt($privateKey);
        $publicKeyDecrypted = $this->encryptor->decrypt($publicKey);

        try {
            $client = new Client();
            $idempotencyKey = uniqid('yuno_', true);

            $response = $client->request('GET', $baseUrl . $userId . '/payment-methods', [
                'headers' => [
                    'X-Idempotency-Key' => $idempotencyKey,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'private-secret-key' => $privateKeyDecrypted,
                    'public-api-key' => $publicKeyDecrypted,
                ],
                'http_errors' => false,
                'timeout' => 5,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode === 200) {
                return $result->setData([
                    'success' => true,
                    'message' => __('Successfully connected to the Yuno API.')
                ]);
            }

            return $result->setData([
                'success' => false,
                'message' => __('Error [%1]: %2', $statusCode, $body)
            ]);
        } catch (\Throwable $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Exception: %1', $e->getMessage())
            ]);
        }
    }
}
