<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Controller\Payment;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\RequestInterface;

class Create implements ActionInterface
{
    /**
     * Create Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     */
    public function __construct(
        protected Context $context,
        protected JsonFactory $resultJsonFactory,
        protected RequestInterface $request
    ) {}

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();

        $params = $this->request->getPostValue();
        $ott = $params['ott'] ?? null;

        return $result->setData([
            'success' => true,
            'message' => 'Payment created successfully',
            'ott' => $ott,
        ]);
    }
}
