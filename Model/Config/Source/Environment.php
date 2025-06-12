<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Data source for selecting Yuno environment: Sandbox or Production
 */
class Environment implements OptionSourceInterface
{
    /**
     * Returns available environment options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'sandbox', 'label' => __('Sandbox')],
            ['value' => 'production', 'label' => __('Production')],
        ];
    }
}
