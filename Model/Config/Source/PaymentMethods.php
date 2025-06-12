<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Data source for payment methods available in Yuno Full Checkout.
 */
class PaymentMethods implements OptionSourceInterface
{
    /**
     * Returns available payment options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'card', 'label' => __('Credit Card')],
            ['value' => 'pix', 'label' => __('Pix')],
            ['value' => 'paypal', 'label' => __('PayPal')],
        ];
    }
}
