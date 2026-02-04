/**
 * Price Tax Wrapper Component
 * Handles tax mode display logic (excluding/including/both)
 * This is a transparent wrapper with no HTML output - pure logic component
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-tax-wrapper
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-tax-wrapper.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-tax-wrapper',
        template: template,

        props: {
            /**
             * Tax display mode
             * @type {String}
             * @required
             * @values 'excluding_tax'|'including_tax'|'both_taxes'
             */
            taxMode: {
                type: String,
                required: true,
                validator: function(value) {
                    return ['excluding_tax', 'including_tax', 'both_taxes'].indexOf(value) !== -1;
                }
            },

            /**
             * Whether this is for tax-inclusive price
             * @type {Boolean}
             * @default false
             */
            includingTax: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            /**
             * Determine if this wrapper should render its content
             * @returns {Boolean}
             */
            shouldRender: function() {
                if (this.taxMode === 'excluding_tax') {
                    return !this.includingTax;
                } else if (this.taxMode === 'including_tax') {
                    return this.includingTax;
                } else if (this.taxMode === 'both_taxes') {
                    return true;
                }
                return true;
            }
        }
    };
});
