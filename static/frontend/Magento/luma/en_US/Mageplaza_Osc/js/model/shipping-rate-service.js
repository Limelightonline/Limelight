/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function (quote, defaultProcessor, customerAddressProcessor, shippingService) {
        'use strict';

        var processors = [];

        processors.default = defaultProcessor;
        processors['customer-address'] = customerAddressProcessor;

        return {
            isAddressChange: false,
            registerProcessor: function (type, processor) {
                processors[type] = processor;
            },
            estimateShippingMethod: function () {
                if (shippingService.isLoading()) {
                    return;
                }

                var type = quote.shippingAddress().getType();

                if (processors[type]) {
                    processors[type].getRates(quote.shippingAddress());
                } else {
                    processors.default.getRates(quote.shippingAddress());
                }
            }
        }
    }
);
