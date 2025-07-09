<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

/**
 * Class TransferFactory
 *
 * This class is responsible for creating transfer objects for HTTP requests to the Yuno gateway.
 */
class TransferFactory implements TransferFactoryInterface
{
    /**
     * TransferFactory constructor.
     *
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        private readonly TransferBuilder $transferBuilder
    ) {}

    /**
     * Creates a transfer object based on the provided request.
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request): TransferInterface
    {
        return $this->transferBuilder
            ->setBody($request['body'] ?? [])
            ->setMethod($request['method'] ?? 'POST')
            ->setUri($request['uri'])
            ->build();
    }
}
