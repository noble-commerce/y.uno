<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provides access to configuration settings for the Yuno Full Checkout payment module.
 */
class ConfigProvider
{
    public const string XML_PATH_ENABLED = 'payment/yuno_full_checkout/yuno_general/enabled';
    public const string XML_PATH_TITLE = 'payment/yuno_full_checkout/yuno_general/title';
    public const string XML_PATH_ENVIRONMENT = 'payment/yuno_full_checkout/yuno_credentials/environment';
    public const string XML_PATH_SANDBOX_PUBLIC_API_KEY = 'payment/yuno_full_checkout/yuno_credentials/sandbox_public_api_key';
    public const string XML_PATH_SANDBOX_PRIVATE_SECRET_KEY = 'payment/yuno_full_checkout/yuno_credentials/sandbox_private_secret_key';
    public const string XML_PATH_PRODUCTION_PUBLIC_API_KEY = 'payment/yuno_full_checkout/yuno_credentials/production_public_api_key';
    public const string XML_PATH_PRODUCTION_PRIVATE_SECRET_KEY = 'payment/yuno_full_checkout/yuno_credentials/production_private_secret_key';
    public const string XML_PATH_ACCOUNT_ID = 'payment/yuno_full_checkout/yuno_credentials/account_id';
    public const string XML_PATH_AUTO_CAPTURE = 'payment/yuno_full_checkout/yuno_behavior/auto_capture';
    public const string XML_PATH_PENDING_STATUS = 'payment/yuno_full_checkout/yuno_behavior/pending_status';
    public const string XML_PATH_ENABLED_METHODS = 'payment/yuno_full_checkout/yuno_behavior/enabled_methods';
    public const string XML_PATH_WEBHOOK_SECRET = 'payment/yuno_full_checkout/yuno_security/webhook_secret';
    public const string XML_PATH_LOG_LEVEL = 'payment/yuno_full_checkout/yuno_logs/log_level';

    /**
     * ConfigProvider Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * IsEnabled Method
     *
     * @param string|null $storeCode
     * @return bool
     */
    public function isEnabled(string $storeCode = null): bool
    {
        return $this->getFlag(self::XML_PATH_ENABLED, $storeCode);
    }

    /**
     * GetTitle Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getTitle(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_TITLE, $storeCode);
    }

    /**
     * GetEnvironment Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getEnvironment(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_ENVIRONMENT, $storeCode);
    }

    /**
     * GetSandboxPublicApiKey Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getSandboxPublicApiKey(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_SANDBOX_PUBLIC_API_KEY, $storeCode);
    }

    /**
     * GetSandboxPrivateSecretKey Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getSandboxPrivateSecretKey(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_SANDBOX_PRIVATE_SECRET_KEY, $storeCode);
    }

    /**
     * GetProductionPublicApiKey Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getProductionPublicApiKey(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_PRODUCTION_PUBLIC_API_KEY, $storeCode);
    }

    /**
     * GetProductionPrivateSecretKey Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getProductionPrivateSecretKey(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_PRODUCTION_PRIVATE_SECRET_KEY, $storeCode);
    }

    /**
     * GetAccountId Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getAccountId(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_ACCOUNT_ID, $storeCode);
    }

    /**
     * IsAutoCapture Method
     *
     * @param string|null $storeCode
     * @return bool
     */
    public function isAutoCapture(string $storeCode = null): bool
    {
        return $this->getFlag(self::XML_PATH_AUTO_CAPTURE, $storeCode);
    }

    /**
     * GetPendingStatus Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getPendingStatus(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_PENDING_STATUS, $storeCode);
    }

    /**
     * GetEnabledMethods Method
     *
     * @param string|null $storeCode
     * @return array
     */
    public function getEnabledMethods(string $storeCode = null): array
    {
        $methods = $this->getValue(self::XML_PATH_ENABLED_METHODS, $storeCode);
        return $methods ? explode(',', $methods) : [];
    }

    /**
     * GetWebhookSecret Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getWebhookSecret(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_WEBHOOK_SECRET, $storeCode);
    }

    /**
     * GetLogLevel Method
     *
     * @param string|null $storeCode
     * @return string|null
     */
    public function getLogLevel(string $storeCode = null): ?string
    {
        return $this->getValue(self::XML_PATH_LOG_LEVEL, $storeCode);
    }

    /**
     * GetValue Method
     *
     * @param string $path
     * @param string|null $storeCode
     * @return string|null
     */
    private function getValue(string $path, ?string $storeCode = null): ?string
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }

    /**
     * GetFlag Method
     *
     * @param string $path
     * @param string|null $storeCode
     * @return bool
     */
    private function getFlag(string $path, ?string $storeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }
}
