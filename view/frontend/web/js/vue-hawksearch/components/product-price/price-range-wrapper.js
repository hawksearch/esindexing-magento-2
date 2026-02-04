/**
 * Price Range Wrapper Component
 * Handles bundle price ranges (from/to) and grouped product containers
 * Provides different wrappers based on product type and range presence
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-range-wrapper
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-range-wrapper.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-range-wrapper',
        template: template,

        props: {
            /**
             * Whether product has a price range
             * @type {Boolean}
             * @default false
             */
            hasRange: {
                type: Boolean,
                default: false
            },

            /**
             * Range display type
             * @type {String}
             * @default null
             * @values 'from'|'to'|'grouped'|null
             */
            rangeType: {
                type: String,
                default: null,
                validator: function(value) {
                    return value === null || ['from', 'to', 'grouped'].indexOf(value) !== -1;
                }
            }
        }
    };
});
