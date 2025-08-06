<?php
/**
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use NobleCommerce\Yuno\Api\Data\YunoTransactionInterface;

/**
 * Interface YunoTransactionRepositoryInterface
 *
 * This interface defines the methods for managing Yuno transactions.
 */
interface YunoTransactionRepositoryInterface
{
    /**
     * Obter uma transação pelo ID.
     *
     * @param int $transactionId
     * @return YunoTransactionInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $transactionId): YunoTransactionInterface;

    /**
     * Salvar uma transação.
     *
     * @param YunoTransactionInterface $transaction
     * @return YunoTransactionInterface
     * @throws CouldNotSaveException
     */
    public function save(YunoTransactionInterface $transaction): YunoTransactionInterface;

    /**
     * Delete a transaction.
     *
     * @param YunoTransactionInterface $transaction
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(YunoTransactionInterface $transaction): bool;

    /**
     * Delete a transaction by ID.
     *
     * @param int $transactionId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $transactionId): bool;
}
