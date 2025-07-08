<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;

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

    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState(Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);

        return $this;
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null): bool
    {
        if (!$this->getConfigData('enabled')) {
            return false;
        }

        return parent::isAvailable($quote);
    }
}
