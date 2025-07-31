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

            if (document.getElementById('yuno-sdk-script')) {
                window.addEventListener('yuno-sdk-ready', () => resolve(true));
                return;
            }

            const script = document.createElement('script');
            script.id = 'yuno-sdk-script';
            script.src = 'https://sdk-web.y.uno/v1.1/main.js';
            script.async = true;
            script.defer = true;

            script.onerror = function (error) {
                const event = new CustomEvent('yuno-sdk-error', { detail: error });
                window.dispatchEvent(event);
                reject(new Error(`Yuno SDK failed to load: ${script.src}`));
            };

            window.addEventListener('yuno-sdk-ready', function () {
                resolve(true);
            });

            document.head.appendChild(script);
        });
    };
});
