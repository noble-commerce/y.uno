<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use NobleCommerce\Yuno\Model\Config\ConfigProvider;

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
     * @param ConfigProvider $config
     */
    public function __construct(
        private readonly ConfigProvider $config
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
        $token = $buildSubject['payment']->getAdditionalInformation('ott');
        $sessionId = $buildSubject['payment']->getAdditionalInformation('sessionId');

        return [
            'body' => json_encode([
                'checkout_session' => $sessionId,
                'one_time_token' => $token,
                'capture' => $this->config->isAutoCapture(),
                'amount' => $amount,
                'currency' => $currency,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config->getProductionPrivateSecretKey()
            ],
            'endpoint' => $this->config->getProductionBaseUrl() . 'payments'
        ];
    }
}
