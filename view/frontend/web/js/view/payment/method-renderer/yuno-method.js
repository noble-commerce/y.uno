/*
* Copyright © 2025 NobleCommerce. All rights reserved.
* See COPYING.txt for license details.
*/
define([
    'Magento_Checkout/js/view/payment/default',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'NobleCommerce_Yuno/payment/yuno'
        },
        initialize: function () {
            this._super();
            this.isChecked.subscribe(function (checked) {
                if (checked) {
                    loadYunoScript();
                }
            });
            return this;
        },
        getCode: function () {
            return 'yuno';
        }
    });

    function loadYunoScript() {
        if (window.Yuno) {
            console.log('Yuno SDK already loaded.');
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdn.y.uno/sdk/v1/yuno-checkout.js';
        script.async = true;
        script.onload = function () {
            console.log('Yuno SDK loaded successfully.');
        };
        document.head.appendChild(script);
    }
});
