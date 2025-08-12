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
use NobleCommerce\Yuno\Model\Config\YunoConfig;
use NobleCommerce\Yuno\Model\Service\YunoClient;
use Throwable;

class Index extends Action
{
    /**
     * Index Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param YunoConfig $configProvider
     * @param EncryptorInterface $encryptor
     * @param YunoClient $yunoClient
     */
    public function __construct(
        private readonly Context $context,
        protected readonly JsonFactory $resultJsonFactory,
        protected readonly YunoConfig $configProvider,
        protected readonly EncryptorInterface $encryptor,
        protected readonly YunoClient $yunoClient,
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
        $userId = $this->configProvider->getUserId();

        try {
            $environmentConfig = $this->yunoClient->getEnvironmentConfig();
            return $this->testConnection(
                $environmentConfig['base_url'],
                $userId,
                $environmentConfig['public_key'],
                $environmentConfig['private_key'],
                $result
            );
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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

        try {
            $client = new Client();
            $headers = $this->yunoClient->buildHeaders($privateKeyDecrypted, $publicKey);

            $response = $client->request('GET', $baseUrl . $userId . '/payment-methods', [
                'headers' => $headers,
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
        } catch (Throwable $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Exception: %1', $e->getMessage())
            ]);
        }
    }
}
