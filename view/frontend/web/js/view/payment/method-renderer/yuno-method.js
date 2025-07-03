/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'jquery'
], function (Component, quote, fullScreenLoader, $) {
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
            return 'yuno_full_checkout';
        },

        /**
         * Overrides the default action of the "Finalize Order" button
         */
        placeOrder: function (data, event) {
            event.preventDefault();

            if (!this.validate() || !this.isPlaceOrderActionAllowed()) {
                return false;
            }

            fullScreenLoader.startLoader();

            window.Yuno.createPaymentToken({
                onSuccess: function (ottResponse) {
                    const ott = ottResponse.token;
                    const sessionId = ottResponse.sessionId;
                    const quoteId = quote.getQuoteId();

                    $.post(
                        '/yuno/payment/create',
                        {
                            token: ott,
                            sessionId: sessionId,
                            quoteId: quoteId
                        }
                    ).done(function (response) {
                        fullScreenLoader.stopLoader();

                        if (response.success && response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message || 'Error creating Yuno payment.');
                        }
                    }).fail(function () {
                        fullScreenLoader.stopLoader();
                        alert('Server communication error.');
                    });
                },
                onError: function (err) {
                    fullScreenLoader.stopLoader();
                    alert('Error generating token: ' + err.message);
                }
            });

            return false;
        }
    });

    function loadYunoScript() {
        if (window.Yuno) {
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdn.y.uno/sdk/v1/yuno-checkout.js';
        script.async = true;
        script.onload = function () {
            console.log('Yuno SDK loaded.');
        };
        document.head.appendChild(script);
    }
});
