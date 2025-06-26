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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use NobleCommerce\Yuno\Model\Config\ConfigProvider;

class Index extends Action
{
    const string ADMIN_RESOURCE = 'NobleCommerce_Yuno::yuno_full_checkout';

    /**
     * Index Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ConfigProvider $configProvider
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly ConfigProvider $configProvider,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $env = $this->configProvider->getEnvironment();

        $publicKey = $env === 'production'
            ? $this->configProvider->getProductionPublicApiKey()
            : $this->configProvider->getSandboxPublicApiKey();

        $privateKeyEncrypted = $env === 'production'
            ? $this->configProvider->getProductionPrivateSecretKey()
            : $this->configProvider->getSandboxPrivateSecretKey();

        $privateKey = $privateKeyEncrypted ? $this->encryptor->decrypt($privateKeyEncrypted) : null;

        if (!$publicKey || !$privateKey) {
            return $result->setData([
                'success' => false,
                'message' => __('Yuno credentials are not configured.')
            ]);
        }

        try {
            $client = new Client();

            $response = $client->request('GET', 'https://api-sandbox.y.uno/v1/payment-methods', [
                'headers' => [
                    'X-Api-Key' => $publicKey,
                    'X-Api-Secret' => $privateKey,
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
