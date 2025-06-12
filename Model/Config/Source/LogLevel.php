<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Data source for Yuno Full Checkout module log level.
 */
class LogLevel implements OptionSourceInterface
{
    /**
     * Returns the available log levels
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'error', 'label' => __('Erros')],
            ['value' => 'debug', 'label' => __('Debug')],
        ];
    }
}
