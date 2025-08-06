<?php
/**
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class YunoTransaction
 *
 * This class represents the resource model for Yuno transactions in Magento.
 * It extends the AbstractDb class to provide database interaction capabilities.
 */
class YunoTransaction extends AbstractDb
{
    /**
     * Initialize the resource model.
     */
    protected function _construct()
    {
        $this->_init('yuno_transactions', 'transaction_id');
    }
}
