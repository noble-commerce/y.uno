<?php
/**
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace NobleCommerce\Yuno\Model;

use NobleCommerce\Yuno\Api\Data\YunoTransactionInterface;
use NobleCommerce\Yuno\Api\YunoTransactionRepositoryInterface;
use NobleCommerce\Yuno\Model\ResourceModel\YunoTransaction as YunoTransactionResource;
use NobleCommerce\Yuno\Model\YunoTransactionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class YunoTransactionRepository
 *
 * This class implements the repository interface for managing Yuno transactions.
 */
class YunoTransactionRepository implements YunoTransactionRepositoryInterface
{
    /**
     * YunoTransactionRepository Constructor
     *
     * @param YunoTransactionResource $resource
     * @param YunoTransactionFactory $transactionFactory
     */
    public function __construct(
        private readonly YunoTransactionResource $resource,
        private readonly YunoTransactionFactory $transactionFactory
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getById(int $transactionId): YunoTransactionInterface
    {
        $transaction = $this->transactionFactory->create();
        $this->resource->load($transaction, $transactionId);

        if (!$transaction->getId()) {
            throw new NoSuchEntityException(__('The transaction with ID "%1" does not exist.', $transactionId));
        }

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function save(YunoTransactionInterface $transaction): YunoTransactionInterface
    {
        try {
            $this->resource->save($transaction);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save the transaction: %1', $e->getMessage()));
        }

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(YunoTransactionInterface $transaction): bool
    {
        try {
            $this->resource->delete($transaction);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete the transaction: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $transactionId): bool
    {
        $transaction = $this->getById($transactionId);
        return $this->delete($transaction);
    }
}
