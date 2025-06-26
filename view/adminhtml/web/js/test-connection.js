/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    "jquery",
    "Magento_Ui/js/modal/alert"
], function ($, alert) {
    "use strict";

    return function (config, element) {
        $(element).on("click", function () {
            $.ajax({
                url: config.url,
                type: "GET",
                dataType: "json",
                showLoader: true
            }).done(function (response) {
                alert({
                    title: response.success
                        ? "✔️ " + $.mage.__("Connection successful")
                        : "❌ " + $.mage.__("Connection error"),
                    content: response.message,
                    buttons: [{
                        text: $.mage.__("OK"),
                        class: response.success ? "action-primary" : "action-secondary",
                        click: function () {
                            this.closeModal();
                        }
                    }]
                });
            }).fail(function (jqXHR, textStatus, errorThrown) {
                alert({
                    title: $.mage.__("Unexpected error"),
                    content: errorThrown,
                    buttons: [{
                        text: $.mage.__("Close"),
                        click: function () {
                            this.closeModal();
                        }
                    }]
                });
            });
        });
    };
});
