<?php
/**
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Api\Data;

/**
 * Interface YunoTransactionInterface
 *
 * This interface defines the required methods
 */
interface YunoTransactionInterface
{
    /**
     * Get the transaction ID.
 *
     * @return int
     */
    public function getTransactionId(): int;

    /**
     * Set the transaction ID.
     *
     * @param int $value
     * @return $this
     */
    public function setTransactionId(int $value);

    /**
     * Get the order ID.
     *
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Set the order ID.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId(int $orderId);


    /**
     * Get the payment status.
     *
     * @return string
     */
    public function getPaymentStatus(): string;

    /**
     * Set the payment status.
     *
     * @param string $paymentStatus
     * @return $this
     */
    public function setPaymentStatus(string $paymentStatus);

    /**
     * Get the creation date.
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set the creation date.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);
}
