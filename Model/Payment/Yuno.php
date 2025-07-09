<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Yuno
 *
 * This class represents the Yuno payment method in Magento.
 * It extends the AbstractMethod class and implements the necessary methods for payment processing.
 */
class Yuno extends AbstractMethod
{
    public const string PAYMENT_METHOD_CODE = 'yuno_full_checkout';

    protected $_code = self::PAYMENT_METHOD_CODE;

    protected $_isOffline = false;

    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = true;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected bool $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;

    /**
     * Initialize
     *
     * @param $paymentAction
     * @param $stateObject
     * @return Yuno|$this
     */
    public function initialize($paymentAction, $stateObject): Yuno|static
    {
        $stateObject->setState(Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);

        return $this;
    }

    /**
     * IsAvailable
     *
     * @param CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(CartInterface $quote = null): bool
    {
        if (!$this->getConfigData('enabled')) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Authorize
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return mixed
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount): mixed
    {
        if (!$amount) {
            throw new LocalizedException(__('Invalid amount for capture.'));
        }

        return $this->gateway->getCommand('yuno_full_checkout')->execute([
            'payment' => $payment,
            'amount' => $amount,
            'currency' => $payment->getOrder()->getOrderCurrencyCode()
        ]);
    }


}
