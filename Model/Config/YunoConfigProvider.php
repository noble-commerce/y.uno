<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use NobleCommerce\Yuno\Model\Config\YunoConfig;

/**
 * Provides configuration settings for integrating the Yuno Full Checkout payment module into the checkout process.
 */
class YunoConfigProvider implements ConfigProviderInterface
{
    /**
     * YunoConfigProvider Constructor.
     *
     * @param YunoConfig $yunoConfig
     */
    public function __construct(
        private readonly YunoConfig $yunoConfig,
    ) {}

    /**
     * Returns the configuration settings for the Yuno Full Checkout payment module.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'yuno_full_checkout' => [
                    'enabled' => $this->yunoConfig->isEnabled(),
                    'title' => $this->yunoConfig->getTitle(),
                    'environment' => $this->yunoConfig->getEnvironment(),
                    'account_id' => $this->yunoConfig->getAccountId(),
                    'sandbox_public_api_key' => $this->yunoConfig->getSandboxPublicApiKey(),
                    'sandbox_base_url' => $this->yunoConfig->getSandboxBaseUrl(),
                    'production_public_api_key' => $this->yunoConfig->getProductionPublicApiKey(),
                    'production_base_url' => $this->yunoConfig->getProductionBaseUrl(),
                    'user_id' => $this->yunoConfig->getUserId(),
                    'enabled_methods' => $this->yunoConfig->getEnabledMethods(),
                ]
            ]
        ];
    }
}
