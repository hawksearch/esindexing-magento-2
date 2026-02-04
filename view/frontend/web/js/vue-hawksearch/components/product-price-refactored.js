/**
 * Product Price Component (Refactored)
 * Main component that orchestrates pricing display using hierarchical components
 * Uses PriceDataProcessor for business logic separation
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price-refactored
 */
define([
    'hawksearchVueSDK',
    'HawkSearch_EsIndexing/js/pricing/PriceDataProcessor',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-amount-wrapper',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-tax-wrapper',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-label',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-container',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-range-wrapper',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price-refactored.html'
], function(
    HawksearchVue,
    PriceDataProcessor,
    PriceAmountWrapper,
    PriceTaxWrapper,
    PriceLabel,
    PriceContainer,
    PriceRangeWrapper,
    template
) {
    'use strict';

    return {
        name: 'product-price-refactored',
        template: template,

        components: {
            'price-amount-wrapper': PriceAmountWrapper,
            'price-tax-wrapper': PriceTaxWrapper,
            'price-label': PriceLabel,
            'price-container': PriceContainer,
            'price-range-wrapper': PriceRangeWrapper
        },

        props: {
            /**
             * Product document from search results
             * @type {Object}
             */
            document: {
                type: Object,
                required: false,
                default: function() {
                    return this.$parent?.result?.Document || {};
                }
            },

            /**
             * Pricing configuration from window.hawksearchConfig
             * @type {Object}
             */
            pricingConfig: {
                type: Object,
                required: false,
                default: function() {
                    return window.hawksearchConfig?.pricing || {};
                }
            }
        },

        data: function() {
            return {
                priceProcessor: null,
                processedPriceData: null,
                processingError: null
            };
        },

        created: function() {
            // Initialize price processor with configuration
            var config = {
                currencySymbol: this.pricingConfig.priceFormat?.currencySymbol || '$',
                currencyPosition: this.pricingConfig.priceFormat?.currencyPosition || 'before',
                decimalPlaces: this.pricingConfig.priceFormat?.decimalPlaces || 2,
                decimalSeparator: this.pricingConfig.priceFormat?.decimalSeparator || '.',
                thousandsSeparator: this.pricingConfig.priceFormat?.thousandsSeparator || ','
            };

            this.priceProcessor = new PriceDataProcessor(config);
        },

        computed: {
            /**
             * Extract raw product data from document
             * Uses window.hawksearch SDK methods with fallback for field access
             * @returns {ProductTypeData|null}
             */
            rawProductData: function() {
                var doc = this.document || {};

                if (!doc || Object.keys(doc).length === 0) {
                    return null;
                }

                // Helper function to safely get document field
                var getField = function(field) {
                    if (window.hawksearch && typeof window.hawksearch.getDocumentField === 'function') {
                        return window.hawksearch.getDocumentField(doc, field);
                    }
                    // Fallback: handle both array and non-array field values
                    var value = doc[field];
                    if (Array.isArray(value) && value.length > 0) {
                        return value[0];
                    }
                    return value !== undefined ? value : null;
                };

                // Extract ID using SDK method with fallback
                var uid;
                if (window.hawksearch && typeof window.hawksearch.extractId === 'function') {
                    uid = window.hawksearch.extractId(doc);
                }
                // Fallback: try both uid and __uid fields
                if (!uid) {
                    uid = getField('uid') || getField('__uid') || '';
                }

                return {
                    type_id: getField('type_id') || '',
                    __uid: uid,
                    price_regular: getField('price_regular'),
                    price_final: getField('price_final'),
                    price_min: getField('price_min'),
                    price_max: getField('price_max')
                };
            },

            /**
             * Tax configuration from pricing config
             * @returns {TaxConfiguration}
             */
            taxConfig: function() {
                return {
                    displayMode: this.pricingConfig.taxDisplayMode || 'excluding_tax',
                    taxRate: this.pricingConfig.mockTaxRate || 0.2,
                    priceIncludesTax: this.pricingConfig.priceIncludesTax || false,
                    decimalPlaces: this.pricingConfig.priceFormat?.decimalPlaces || 2
                };
            },

            /**
             * Process price data through PriceDataProcessor
             * @returns {PriceData|null}
             */
            priceData: function() {
                if (!this.rawProductData || !this.priceProcessor) {
                    return null;
                }

                try {
                    return this.priceProcessor.process(this.rawProductData, this.taxConfig);
                } catch (error) {
                    console.error('Error processing price data:', error);
                    this.processingError = error;
                    return null;
                }
            },

            /**
             * Check if product type is supported
             * @returns {Boolean}
             */
            isSupported: function() {
                return this.priceData !== null && !this.processingError;
            }
        }
    };
});
