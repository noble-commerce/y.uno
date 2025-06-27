<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use NobleCommerce\Yuno\Model\Config\ConfigProvider as YunoBackendConfig;

class ConfigProvider implements ConfigProviderInterface
{
    public const string CODE = 'yuno_full_checkout';

    public function __construct(
        private readonly YunoBackendConfig $config
    ) {}

    public function getConfig(): array
    {
        $env = $this->config->getEnvironment();
        $publicKey = $env === 'production'
            ? $this->config->getProductionPublicApiKey()
            : $this->config->getSandboxPublicApiKey();

        return [
            'payment' => [
                self::CODE => [
                    'title' => $this->config->getTitle(),
                    'environment' => $env,
                    'publicKey' => $publicKey,
                    'accountId' => $this->config->getAccountId(),
                    'autoCapture' => $this->config->isAutoCapture(),
                    'enabledMethods' => $this->config->getEnabledMethods(),
                    'pendingStatus' => $this->config->getPendingStatus()
                ]
            ]
        ];
    }
}
