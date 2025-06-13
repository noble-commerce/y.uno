<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Controller\Webhook;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

class Notify implements ActionInterface
{
    /**
     * Notify Constructor
     *
     * @param RequestInterface $request
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected RequestInterface $request,
        protected ResultFactory $resultFactory,
        protected LoggerInterface $logger
    ) {}

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $input = $this->request->getContent();
        $this->logger->info('[YUNO] Webhook received: ' . $input);

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setContents('Webhook received successfully');
    }
}
