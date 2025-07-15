<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class CaptureCommand
 *
 * This class handles the capture command execution for the Yuno gateway.
 * It processes the capture request and returns the result.
 */
class CaptureCommand implements CommandInterface
{
    /**
     * CaptureCommand constructor.
     *
     * @param ClientInterface $client
     * @param TransferFactoryInterface $transferFactory
     */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly TransferFactoryInterface $transferFactory
    ) {}

    /**
     * Executes the capture command.
     *
     * @param array $commandSubject
     * @return array
     * @throws CommandException
     */
    public function execute(array $commandSubject): array
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $commandSubject['payment'];
        $payment = $paymentDO->getPayment();

        $amount = $commandSubject['amount'];

        $transfer = $this->transferFactory->create([
            'uri'    => 'https://api.y.uno/payments/capture',
            'method' => 'POST',
            'body'   => [
                'payment_id' => $payment->getLastTransId(),
                'amount' => $amount,
            ]
        ]);

        try {
            $response = $this->client->placeRequest($transfer);
        } catch (ClientException|ConverterException $e) {
            throw new CommandException(__('Yuno capture error: %1', $e->getMessage()));
        }

        if (empty($response) || !isset($response['status']) || $response['status'] !== 'success') {
            throw new CommandException(__('Yuno capture failed.'));
        }

        return $response;
    }
}
