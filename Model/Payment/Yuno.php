<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use Magento\Quote\Api\Data\CartInterface;
use NobleCommerce\Yuno\Model\Config\YunoConfig;

/**
 * Class Yuno
 *
 * This class represents the Yuno payment method in Magento.
 * It extends the AbstractMethod class and implements the necessary methods for payment processing.
 */
class Yuno extends AbstractMethod
{
    public const PAYMENT_METHOD_CODE = 'yuno_full_checkout';
    protected $_code = self::PAYMENT_METHOD_CODE;
    protected $_isOffline = false;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = true;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_isInitializeNeeded = true;

    /**
     * Yuno constructor.
     *
     * @param YunoConfig $configProvider
     */
    public function __construct(
        private readonly YunoConfig $configProvider
    ) {}

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
     * This method checks if the payment method is available for the given quote.
     *
     * @param CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(CartInterface $quote = null): bool
    {
        if (!$quote) {
            return false;
        }

        $storeCode = $quote->getStore()->getCode();

        return $this->configProvider->isEnabled($storeCode);
    }

    /**
     * CanUseCheckout
     *
     * This method checks if the payment method can be used during checkout.
     *
     * @return bool
     */
    public function canUseCheckout(): bool
    {
        return true;
    }
}
