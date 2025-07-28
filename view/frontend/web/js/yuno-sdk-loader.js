/*
  Copyright © 2025 NobleCommerce. All rights reserved.
  See COPYING.txt for license details.
*/
define([], function () {
    'use strict';

    return function injectYunoScript() {
        return new Promise(function (resolve, reject) {
            if (window.yuno) {
                resolve(true);
                return;
            }

            const head = document.getElementsByTagName('head')[0];
            const js = document.createElement('script');
            js.src = "https://sdk-web.y.uno/v1.1/main.js";
            js.defer = true;

            js.onerror = function (error) {
                const event = new CustomEvent('yuno-sdk-error', { detail: error });
                window.dispatchEvent(event);
                reject(new Error(`Failed to load script: ${js.src}`));
            };

            window.addEventListener('yuno-sdk-ready', function () {
                resolve(true);
            });

            head.appendChild(js);
        });
    };
});
