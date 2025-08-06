<?php
/**
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use NobleCommerce\Yuno\Api\Data\YunoTransactionInterface;
use NobleCommerce\Yuno\Model\ResourceModel\YunoTransaction as YunoTransactionResource;

/**
 * Class YunoTransaction
 *
 * This class represents a Yuno transaction model in Magento.
 * It extends the AbstractModel class and implements the YunoTransactionInterface.
 */
class YunoTransaction extends AbstractModel implements YunoTransactionInterface
{
    /**
     * YunoTransaction constructor.
     *
     * @param ConfigProvider $configProvider
     * @throws LocalizedException
     */
    protected function _construct()
    {
        $this->_init(YunoTransactionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId(): int
    {
        return (int) $this->getData('transaction_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId(int $transactionId)
    {
        return $this->setData('transaction_id', $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId(): int
    {
        return (int) $this->getData('order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId(int $orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentStatus(): string
    {
        return (string) $this->getData('payment_status');
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentStatus(string $paymentStatus)
    {
        return $this->setData('payment_status', $paymentStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): string
    {
        return (string) $this->getData('created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(string $createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }
}
