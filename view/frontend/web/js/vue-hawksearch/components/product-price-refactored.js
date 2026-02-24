/**
 * Copyright (c) 2026 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

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
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-label',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-container',
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-range-wrapper',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price-refactored.html'
], function(
    HawksearchVue,
    PriceDataProcessor,
    PriceAmountWrapper,
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

        created: function() {
            this.priceProcessor = new PriceDataProcessor(this.pricingConfig);
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
                    taxRate: this.pricingConfig.mockTaxRate || 0,
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
        },

        /**
         * Extract a render ready containers
         * @param {PriceRangeItem|null} range - Range data or null for a root containers
         * @return {Object[]}
         */
        methods: {
            buildPriceRangeWrapperProps: function(range) {
                var elementClass = 'price-box';
                if (range.rangeItemType === 'from' && this.priceData.type === 'bundle') {
                    elementClass = 'price-form';
                } else if (range.rangeItemType === 'to' && this.priceData.type === 'bundle') {
                    elementClass = 'price-to';
                }
                return {
                    tag:  this.priceData.type === 'grouped' ? 'div' : 'p',
                    class: elementClass
                }
            },

            extractPriceContainers: function(range){
                var containers = [this._buildFinalPriceContainer(range)];
                if (this.priceData.discount.hasDiscount) {
                    containers.push(this._buildRegularPriceContainer(range));
                }

                containers.forEach(function (container) {
                    var priceWrapperIncludingTax = this._buildIncludingTaxPriceWrapper(range, container);
                    var priceWrapperExcludingTax = this._buildExcludingTaxPriceWrapper(range, container);
                    if (this.taxConfig.displayMode === 'both_taxes' || this.taxConfig.displayMode === 'including_tax') {
                        container.priceWrappers.push(priceWrapperIncludingTax);
                    }
                    if (this.taxConfig.displayMode === 'both_taxes' || this.taxConfig.displayMode === 'excluding_tax') {
                        container.priceWrappers.push(priceWrapperExcludingTax);
                    }
                }.bind(this));

                return containers;
            },

            _buildFinalPriceContainer: function(range){
                var label = '';
                var props = {
                    wrapperElementTag: '',
                    labelPosition: 'inner',
                }
                if (this.priceData.discount.hasDiscount) {
                    label = 'Special Price';
                    if (this.priceData.type !== 'bundle') {
                        props.wrapperElementTag = 'span';
                        props.wrapperElementClass = 'special-price';
                    }
                }
                if (this.priceData.type === 'configurable' && !this.priceData.samePriceForAll) {
                    label = 'As low as';
                }

                if (range?.rangeItemType === 'from') {
                    label = 'From';
                    if (this.priceData.type === 'grouped') {
                        label = 'Starting at';
                        props.wrapperElementTag = 'p'
                        props.wrapperElementClass = 'minimal-price';
                        props.labelPosition = 'outer';
                    }
                } else if (range?.rangeItemType === 'to') {
                    label = 'To';
                }
                return {
                    type: 'final',
                    props: props,
                    label: label,
                    priceWrappers: []
                }
            },

            _buildRegularPriceContainer: function(range){
                var label = 'Regular Price';
                return {
                    type: 'regular',
                    props: {
                        wrapperElementTag: 'span',
                        wrapperElementClass: 'old-price',
                        labelPosition: 'inner'
                    },
                    label: label,
                    priceWrappers: []
                }
            },

            /**
             *
             * @param {PriceRangeItem|null} range
             * @return {{amount: string, priceType: string, elementId: string, labelText: (string), class: *[]}}
             * @private
             */
            _buildIncludingTaxPriceWrapper: function(range, container){
                var classes = [];
                if (this.taxConfig.displayMode === 'both_taxes') {
                    classes.push('price-including-tax');
                }
                var priceType = '';
                var containerType = container.type; //final, regular
                // , min_final, min_regular, max_final, max_regular
                switch (containerType) {
                    case 'final':
                        priceType = 'finalPrice';
                        break;
                    default: // regular
                        priceType = 'oldPrice';
                        if (range) {
                            priceType = '';
                        }
                        break;
                }
                if (range) {
                    var findPattern = 'finalPrice',
                        replacePattern = range.rangeItemType === 'from'
                            ? 'minPrice'
                            : range.rangeItemType === 'to'
                                ? 'maxPrice'
                                : '';
                    if (replacePattern) {
                        priceType.replace(findPattern, replacePattern);
                    }
                }

                var elementIdPattern = this.taxConfig.displayMode === 'both_taxes' ? 'price-including-tax' : '';

                var amount = null;
                if (containerType === 'final') {
                    amount = range ? range.amountIncludingTax : this.priceData.finalPriceIncludingTax;
                }
                if (containerType === 'regular') {
                    amount = range ? range.regularAmountIncludingTax : this.priceData.regularPriceIncludingTax;
                }

                return {
                    amount: amount,
                    elementId: this._buildPriceWrapperElementId(range, container, elementIdPattern),
                    priceType: priceType,
                    labelText: this.taxConfig.displayMode === 'both_taxes' ? 'Incl. Tax' : '',
                    class: classes
                }
            },

            /**
             *
             * @param {PriceRangeItem|null} range
             * @return {{amount: string, priceType: string, elementId: string, labelText: (string), class: *[]}}
             * @private
             */
            _buildExcludingTaxPriceWrapper: function(range, container){
                var classes = [];
                if (this.taxConfig.displayMode === 'both_taxes') {
                    classes.push('price-excluding-tax');
                }
                var priceType = '';
                var containerType = container.type; //final, regular
                switch (containerType) {
                    case 'final':
                        priceType = this.taxConfig.displayMode === 'both_taxes' ? 'basePrice' : 'finalPrice';
                        break;
                    case 'regular':
                        priceType = this.taxConfig.displayMode === 'both_taxes' ? 'baseOldPrice' : 'oldPrice';
                        if (range) {
                            priceType = '';
                        }
                        break;
                }
                if (range) {
                    var findPattern = 'basePrice',
                        replacePattern = range.rangeItemType === 'from'
                            ? 'baseMinPrice'
                            : range.rangeItemType === 'to'
                                ? 'baseMaxPrice'
                                : '';
                    if (replacePattern) {
                        priceType.replace(findPattern, replacePattern);
                    }
                }

                // [priceType]
                /*
                * --final_container--
                * finalPrice: !both_taxes || (both_taxes && incl_tax)
                * basePrice: both_taxes && excl_tax
                * --regular_container--
                * oldPrice: !both_taxes || (both_taxes && incl_tax)
                * baseOldPrice: both_taxes && excl_tax
                * */

                // [priceType] - grouped
                /*
                * --final_container--
                * <empty>
                * --regular_container--
                * <empty>
                * */

                // [priceType] - bundle
                /*
                * --minFinal_container--
                * minPrice: !both_taxes || (both_taxes && incl_tax)
                * baseMinPrice: both_taxes && excl_tax
                * --maxFinal_container--
                * maxPrice: !both_taxes || (both_taxes && incl_tax)
                * baseMaxPrice: both_taxes && excl_tax
                * --minRegular_container--
                * <empty>
                * --maxRegular_container--
                * <empty>
                * */

                var elementIdPattern = this.taxConfig.displayMode === 'both_taxes' ? 'price-excluding-tax' : '';

                var amount = null;
                if (containerType === 'final') {
                    amount = range ? range.amount : this.priceData.finalPrice;
                }
                if (containerType === 'regular') {
                    amount = range ? range.regularAmount : this.priceData.regularPrice;
                }

                return {
                    amount: amount,
                    elementId: this._buildPriceWrapperElementId(range, container, elementIdPattern),
                    priceType: priceType,
                    labelText: this.taxConfig.displayMode === 'both_taxes' ? 'Excl. Tax': '',
                    class: classes,
                }
            },

            _buildPriceWrapperElementId: function(range, container, taxPattern){
                var priceTypePatern = 'product-price';

                if (range?.rangeItemType === 'from') {
                    priceTypePatern = 'from';
                }
                if (range?.rangeItemType === 'to') {
                    priceTypePatern = 'to';
                }

                if (container.type === 'regular') {
                    priceTypePatern = 'old-price';
                }

                if (this.priceData.type === 'grouped') {
                    priceTypePatern = '';
                }

                var patterns = [
                    taxPattern,
                    priceTypePatern,
                    this.priceData.uid
                ];
                if (patterns.slice(0, -1).filter(Boolean).join('') === '') {
                    patterns = [];
                }

                return patterns.filter(Boolean).join('-');
            },
        }
    };
});
