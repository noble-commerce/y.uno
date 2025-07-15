<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\ResultInterface;

/**
 * Class ResponseValidator
 *
 * This class validates the response from the Yuno payment gateway.
 * It checks if the response status is 'CREATED' to determine if the payment was successful.
 */
class ResponseValidator implements ValidatorInterface
{
    /**
     * ResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        private readonly ResultInterfaceFactory $resultFactory
    ) {}

    /**
     * Validates the response from the payment gateway.
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = $validationSubject['response'] ?? [];
        $validStatuses = ['CREATED', 'APPROVED', 'SUCCESS'];
        $isValid = isset($response['status']) && in_array($response['status'], $validStatuses, true);

        $errors = !$isValid
            ? [
                'Payment failed or returned invalid status: ' . ($response['status'] ?? 'undefined')
            ]
            : [];
        return $this->resultFactory->create(['isValid' => $isValid, 'failsDescription' => $errors]);
    }
}
