<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Service;

use Exception;
use Magento\Framework\HTTP\Client\Curl;
use NobleCommerce\Yuno\Model\Config\YunoConfig;

/**
 * YunoClient Class
 *
 * Handles communication with the Yuno API.
 */
class YunoClient
{
    /**
     * YunoClient Constructor
     *
     * @param Curl $curl
     * @param YunoConfig $yunoConfig
     */
    public function __construct(
        private readonly Curl $curl,
        private readonly YunoConfig $yunoConfig
    ) {}

    /**
     * Create Customer
     *
     * @param array $customerData
     * @return array
     * @throws Exception
     */
    public function createCustomer(array $customerData): array
    {
        $environmentConfig = $this->getEnvironmentConfig();
        $headers = $this->buildHeaders($environmentConfig['private_key'], $environmentConfig['public_key']);

        $this->curl->setHeaders($headers);
        $payload = json_encode($customerData);
        $this->curl->post($environmentConfig['base_url'], $payload);

        $statusCode = $this->curl->getStatus();
        $response = $this->curl->getBody();

        if ($statusCode === 201) {
            return json_decode($response, true);
        } else {
            throw new Exception(__('Error creating Customer: ') . $response);
        }
    }

    /**
     * Build Headers
     *
     * @param string $privateKeyDecrypted
     * @param string $publicKey
     * @return array
     */
    public function buildHeaders(string $privateKeyDecrypted, string $publicKey): array
    {
        return [
            'X-Idempotency-Key' => uniqid('yuno_', true),
            'accept' => 'application/json',
            'charset' => 'utf-8',
            'content-type' => 'application/json',
            'private-secret-key' => $privateKeyDecrypted,
            'public-api-key' => $publicKey,
        ];
    }

    /**
     * Get Environment Config
     *
     * @return array
     * @throws Exception
     */
    public function getEnvironmentConfig(): array
    {
        $environment = $this->yunoConfig->getEnvironment();

        if ($environment === YunoConfig::ENVIRONMENT_SANDBOX) {
            return [
                'base_url' => $this->yunoConfig->getSandboxBaseUrl(),
                'public_key' => $this->yunoConfig->getSandboxPublicApiKey(),
                'private_key' => $this->yunoConfig->getSandboxPrivateSecretKey(),
            ];
        }

        if ($environment === YunoConfig::ENVIRONMENT_PRODUCTION) {
            return [
                'base_url' => $this->yunoConfig->getProductionBaseUrl(),
                'public_key' => $this->yunoConfig->getProductionPublicApiKey(),
                'private_key' => $this->yunoConfig->getProductionPrivateSecretKey(),
            ];
        }
        throw new Exception(__('Invalid environment configuration.'));
    }
}
