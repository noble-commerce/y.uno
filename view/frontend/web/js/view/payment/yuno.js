/*
* Copyright © 2025 NobleCommerce. All rights reserved.
* See COPYING.txt for license details.
*/
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'yuno',
        component: 'NobleCommerce_Yuno/js/view/payment/method-renderer/yuno-method'
    });

    return Component.extend({});
});
