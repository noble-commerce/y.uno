<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Command\ResultInterfaceFactory;
use Psr\Log\LoggerInterface;

/**
 * Class PayCommand
 *
 * This class handles the payment command execution for the Yuno gateway.
 * It processes the payment request, validates the response, and returns the result.
 */
class PayCommand implements CommandInterface
{
    /**
     * PayCommand constructor.
     *
     * @param ClientInterface $client
     * @param TransferFactoryInterface $transferFactory
     * @param ValidatorInterface $validator
     * @param ResultInterfaceFactory $resultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ClientInterface $client,
        private TransferFactoryInterface $transferFactory,
        private ValidatorInterface $validator,
        private ResultInterfaceFactory $resultFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * Executes the payment command.
     *
     * @param array $commandSubject
     * @return ResultInterface
     * @throws CommandException
     */
    public function execute(array $commandSubject): ResultInterface
    {
        try {
            $transfer = $this->transferFactory->create($commandSubject);
            $response = $this->client->placeRequest($transfer);

            $validationResult = $this->validator->validate([
                'response' => $response,
                'command_subject' => $commandSubject
            ]);

            if (!$validationResult->isValid()) {
                throw new CommandException(
                    implode('; ', $validationResult->getFailsDescription())
                );
            }

            return $this->resultFactory->create(['response' => $response]);

        } catch (\Throwable $e) {
            $this->logger->error('[Yuno] Payment execution failed: ' . $e->getMessage());
            throw new CommandException(
                __('Yuno payment failed: %1', $e->getMessage())
            );
        }
    }
}
