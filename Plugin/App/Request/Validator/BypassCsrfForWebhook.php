<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Plugin\App\Request\Validator;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\ValidatorInterface;

/**
 * Around plugin to validate method to ignore CSRF in webhook.
 */
class BypassCsrfForWebhook
{
    /**
     * AroundValidate
     *
     * @param ValidatorInterface $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @param ...$arguments
     * @return void
     */
    public function aroundValidate(
        ValidatorInterface $subject,
        callable $proceed,
        RequestInterface $request,
        ...$arguments
    ) {
        if ($request->getFullActionName() === 'yuno_webhook_notify') {
            return;
        }
        return $proceed($request, ...$arguments);
    }
}
