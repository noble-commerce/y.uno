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
    public const string ADMIN_RESOURCE = 'NobleCommerce_Yuno::yuno_full_checkout';

    protected JsonFactory $resultJsonFactory;
    protected ConfigProvider $configProvider;
    protected EncryptorInterface $encryptor;

    private const string SANDBOX_URL = 'https://api-sandbox.y.uno/v1/customers/';
    private const string PRODUCTION_URL = 'https://api.y.uno/v1/customers/';

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigProvider $configProvider,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configProvider = $configProvider;
        $this->encryptor = $encryptor;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $environment = $this->configProvider->getEnvironment();
        $userId = $this->configProvider->getUserId();

        if ($environment === 'sandbox') {
            return $this->testConnection(
                self::SANDBOX_URL,
                $userId,
                $this->configProvider->getSandboxPublicApiKey(),
                $this->configProvider->getSandboxPrivateSecretKey(),
                $result
            );
        }

        if ($environment === 'production') {
            return $this->testConnection(
                self::PRODUCTION_URL,
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

    private function testConnection(
        string $baseUrl,
        ?string $userId,
        ?string $publicKey,
        ?string $privateKey,
        $result
    ) {
        if (!$publicKey || !$privateKey) {
            return $result->setData([
                'success' => false,
                'message' => __('Yuno credentials are not configured.')
            ]);
        }

        $privateKeyDecrypted = $this->encryptor->decrypt($privateKey);

        try {
            $client = new Client();
            $idempotencyKey = uniqid('yuno_', true);

            $response = $client->request('GET', $baseUrl . $userId . '/payment-methods', [
                'headers' => [
                    'X-Idempotency-Key' => $idempotencyKey,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'private-secret-key' => $privateKeyDecrypted,
                    'public-api-key' => $publicKey,
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
