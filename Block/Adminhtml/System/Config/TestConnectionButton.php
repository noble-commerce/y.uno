<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;

class TestConnectionButton extends Field
{
    /**
     * TestConnectionButton Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * _getElementHtml
     *
     * @param AbstractElement $element
     * @return string
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->setData('button_id', 'yuno-test-connection-button');
        $this->setData('ajax_url', $this->getUrl('yuno/testconnection/index'));

        return $this->getLayout()
            ->createBlock(Template::class)
            ->setTemplate('NobleCommerce_Yuno::system/config/test_connection_button.phtml')
            ->setData($this->getData())
            ->toHtml();
    }
}
