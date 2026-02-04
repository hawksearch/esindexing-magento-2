/**
 * Price Amount Wrapper Component
 * Displays a single price amount with proper HTML structure and data attributes
 * This is a leaf component in the pricing hierarchy
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-amount-wrapper
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-amount-wrapper.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-amount-wrapper',
        template: template,

        props: {
            /**
             * Numeric price amount
             * @type {Number}
             * @required
             */
            amount: {
                type: Number,
                required: true,
                validator: function(value) {
                    return value >= 0;
                }
            },

            /**
             * Formatted price string (e.g., "$32.00")
             * @type {String}
             * @required
             */
            formattedPrice: {
                type: String,
                required: true
            },

            /**
             * Price type identifier for data attribute
             * @type {String}
             * @default 'final'
             */
            priceType: {
                type: String,
                default: 'final'
            }
        }
    };
});
