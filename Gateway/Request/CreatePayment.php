<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use NobleCommerce\Yuno\Model\Config\YunoConfig;

/**
 * Class CreatePayment
 *
 * This class builds the request for creating a payment in the Yuno gateway.
 */
class CreatePayment implements BuilderInterface
{
    /**
     * CreatePayment constructor.
     *
     * @param YunoConfig $config
     */
    public function __construct(
        private readonly YunoConfig $config
    ) {}

    /**
     * Builds the request for creating a payment.
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $amount = $buildSubject['amount'];
        $currency = $buildSubject['currency'];
        $payment = $buildSubject['payment'];
        $token = $payment->getAdditionalInformation('ott');
        $sessionId = $payment->getAdditionalInformation('sessionId');
        $storeCode = $payment->getOrder()?->getStore()->getCode();

        $isProduction = $this->config->getEnvironment($storeCode) === 'production';
        $baseUrl = $isProduction
            ? rtrim((string) $this->config->getProductionBaseUrl($storeCode), '/')
            : rtrim((string) $this->config->getSandboxBaseUrl($storeCode), '/');
        $privateKey = $isProduction
            ? (string) $this->config->getProductionPrivateSecretKey($storeCode)
            : (string) $this->config->getSandboxPrivateSecretKey($storeCode);

        return [
            'body' => [
                'checkout_session' => $sessionId,
                'one_time_token' => $token,
                'capture' => $this->config->isAutoCapture($storeCode),
                'amount' => $amount,
                'currency' => $currency,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $privateKey
            ],
            'uri' => $baseUrl . '/payments'
        ];
    }
}
