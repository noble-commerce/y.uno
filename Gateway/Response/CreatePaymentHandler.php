<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Class CreatePaymentHandler
 *
 * Responsible for handling Yuno's payment creation response.
 */
class CreatePaymentHandler implements HandlerInterface
{
    /**
     * Handles the payment creation response.
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response): void
    {
        if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) {
            return;
        }
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        if (isset($response['payment_id'])) {
            $payment->setTransactionId($response['payment_id']);
            $payment->setIsTransactionClosed(false);
        }

        if (isset($response['redirect_url'])) {
            $payment->setAdditionalInformation('redirect_url', $response['redirect_url']);
        }
    }
}

