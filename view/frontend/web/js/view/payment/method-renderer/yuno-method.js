/*
* Copyright © 2025 NobleCommerce. All rights reserved.
* See COPYING.txt for license details.
*/
define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'NobleCommerce_Yuno/js/yuno-sdk-loader'
], function (Component, $, loadSdk) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'NobleCommerce_Yuno/payment/yuno',
            transactionResult: ''
        },

        initialize: function () {
            this._super();
            console.log('Yuno payment method component initialized');
            const config = window.checkoutConfig?.payment?.yuno_full_checkout;
            const publicKey = config?.publicKey;

            if (!publicKey) {
                console.error('Yuno publicKey is not defined in checkoutConfig');
                return;
            }

            if (window.yuno && window.yuno._initialized) {
                console.info('Yuno SDK already loaded.');
                return;
            }

            $(document).ready(() => {
                loadSdk()
                    .then(() => yuno.initialize(publicKey))
                    .then(() => {
                        if (!document.getElementById('yuno-apm-form')) {
                            $('<div id="yuno-apm-form"></div>').appendTo('#yuno-checkout-container');
                        }
                        if (!document.getElementById('yuno-action-form')) {
                            $('<div id="yuno-action-form"></div>').appendTo('#yuno-checkout-container');
                        }

                        return yuno.startCheckout({
                            renderMode: {
                                type: 'element',
                                elementSelector: {
                                    apmForm: '#yuno-apm-form',
                                    actionForm: '#yuno-action-form'
                                }
                            }
                        });
                    })
                    .then(() => {
                        window.yuno._initialized = true;
                        console.log('Yuno SDK initialized and rendered');
                    })
                    .catch((e) => {
                        console.error('Error initializing or rendering Yuno SDK:', e);
                    });
            });

            return this;
        },

        initObservable: function () {
            this._super()
                .observe([
                    'transactionResult'
                ]);
            return this;
        },

        getCode: function () {
            return 'yuno_full_checkout';
        },

        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'transaction_result': this.transactionResult()
                }
            };
        },

        getTransactionResults: function () {
            const config = window.checkoutConfig?.payment?.yuno_full_checkout;
            return _.map(config?.transactionResults || {}, function (value, key) {
                return {
                    'value': key,
                    'transaction_result': value
                };
            });
        }
    });
});
