/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price.html'
], function (HawksearchVue, template) {
    'use strict';

    // Mock tax calculation logic
    function calculateTaxInclusive(price, taxRate) {
        if (price == null || isNaN(price)) return null;
        return Number(price) * (1 + taxRate);
    }

    function formatPrice(value, priceFormat) {
        if (value == null || isNaN(value)) return '';
        return new Intl.NumberFormat(priceFormat?.locale || 'en-US', {
            style: 'currency',
            currency: priceFormat?.currency || 'USD',
            minimumFractionDigits: 2
        }).format(Number(value));
    }

    return {
        name: 'product-price',
        template: template,
        props: {
            document: {
                type: Object,
                required: false,
                default: function() { return this.$parent?.result?.Document || {}; }
            },
            pricingConfig: {
                type: Object,
                required: false,
                default: function() { return window.hawksearchConfig?.pricing || {}; }
            }
        },
        computed: {
            priceData() {
                // Extract fields from document
                const doc = this.document || {};
                const typeId = doc.type_id || '';
                const price_regular = doc.price_regular ?? null;
                const price_final = doc.price_final ?? null;
                const price_min = doc.price_min ?? null;
                const price_max = doc.price_max ?? null;
                const priceFormat = this.pricingConfig.priceFormat || { currency: 'USD', locale: 'en-US' };
                const taxDisplayMode = this.pricingConfig.taxDisplayMode || 'excluding_tax';
                const uid = doc.uid || doc.__uid || '';
                // Configurable mock tax rate (can be replaced with real service)
                const mockTaxRate = this.pricingConfig.mockTaxRate ?? 0.2; // 20% default
                // Helper for formatted price
                const fmt = v => formatPrice(v, priceFormat);
                const fmtTax = v => formatPrice(calculateTaxInclusive(v, mockTaxRate), priceFormat);
                // Helper for both prices
                function bothPrices(excl, incl, labelExcl, labelIncl) {
                    return {
                        price_excl: excl,
                        price_excl_formatted: fmt(excl),
                        price_incl: calculateTaxInclusive(excl, mockTaxRate),
                        price_incl_formatted: fmtTax(excl),
                        label_excl: labelExcl,
                        label_incl: labelIncl
                    };
                }

                // Simple/Virtual/Downloadable
                if (["simple", "virtual", "downloadable"].includes(typeId)) {
                    if (price_regular && price_final && price_regular !== price_final) {
                        // Discounted
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'discount_incl',
                                uid,
                                price_final,
                                price_final_formatted: fmtTax(price_final),
                                price_regular,
                                price_regular_formatted: fmtTax(price_regular)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'discount_both',
                                uid,
                                ...bothPrices(price_final, null, 'Excl. Tax', 'Incl. Tax'),
                                price_regular,
                                price_regular_formatted: fmt(price_regular),
                                price_regular_incl_formatted: fmtTax(price_regular)
                            };
                        } else {
                            // Excluding tax
                            return {
                                type: typeId,
                                template: 'discount',
                                uid,
                                price_final,
                                price_final_formatted: fmt(price_final),
                                price_regular,
                                price_regular_formatted: fmt(price_regular)
                            };
                        }
                    } else {
                        // No discount
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'regular_incl',
                                uid,
                                price_final,
                                price_final_formatted: fmtTax(price_final)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'regular_both',
                                uid,
                                ...bothPrices(price_final, null, 'Excl. Tax', 'Incl. Tax')
                            };
                        } else {
                            return {
                                type: typeId,
                                template: 'regular',
                                uid,
                                price_final,
                                price_final_formatted: fmt(price_final)
                            };
                        }
                    }
                }

                // Giftcard
                if (typeId === 'giftcard') {
                    if (price_final !== null && price_min !== null && price_max == null) {
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'giftcard_incl',
                                uid,
                                price_min,
                                price_min_formatted: fmtTax(price_min)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'giftcard_both',
                                uid,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax')
                            };
                        } else {
                            return {
                                type: typeId,
                                template: 'giftcard',
                                uid,
                                price_min,
                                price_min_formatted: fmt(price_min)
                            };
                        }
                    }
                }

                // Bundle
                if (typeId === 'bundle') {
                    if (price_regular > 0 && price_final > 0 && price_regular !== price_final && price_min > 0 && price_max > 0) {
                        const discount_multiplier = price_final / price_regular;
                        const price_min_regular = (price_min / discount_multiplier).toFixed(2);
                        const price_max_regular = (price_max / discount_multiplier).toFixed(2);
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'bundle_discount_incl',
                                uid,
                                price_min,
                                price_max,
                                price_min_formatted: fmtTax(price_min),
                                price_max_formatted: fmtTax(price_max),
                                price_min_regular,
                                price_max_regular,
                                price_min_regular_formatted: fmtTax(price_min_regular),
                                price_max_regular_formatted: fmtTax(price_max_regular)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'bundle_discount_both',
                                uid,
                                price_min,
                                price_max,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax'),
                                price_max_both: bothPrices(price_max, null, 'Excl. Tax', 'Incl. Tax'),
                                price_min_regular,
                                price_max_regular,
                                price_min_regular_formatted: fmt(price_min_regular),
                                price_max_regular_formatted: fmt(price_max_regular),
                                price_min_regular_incl_formatted: fmtTax(price_min_regular),
                                price_max_regular_incl_formatted: fmtTax(price_max_regular)
                            };
                        } else {
                            return {
                                type: typeId,
                                template: 'bundle_discount',
                                uid,
                                price_min,
                                price_max,
                                price_min_formatted: fmt(price_min),
                                price_max_formatted: fmt(price_max),
                                price_min_regular,
                                price_max_regular,
                                price_min_regular_formatted: fmt(price_min_regular),
                                price_max_regular_formatted: fmt(price_max_regular)
                            };
                        }
                    } else if (price_min > 0 && price_max > 0) {
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'bundle_incl',
                                uid,
                                price_min,
                                price_max,
                                price_min_formatted: fmtTax(price_min),
                                price_max_formatted: fmtTax(price_max)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'bundle_both',
                                uid,
                                price_min,
                                price_max,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax'),
                                price_max_both: bothPrices(price_max, null, 'Excl. Tax', 'Incl. Tax')
                            };
                        } else {
                            return {
                                type: typeId,
                                template: 'bundle',
                                uid,
                                price_min,
                                price_max,
                                price_min_formatted: fmt(price_min),
                                price_max_formatted: fmt(price_max)
                            };
                        }
                    }
                }

                // Grouped
                if (typeId === 'grouped') {
                    if (price_min > 0) {
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: 'grouped_incl',
                                uid,
                                price_min,
                                price_min_formatted: fmtTax(price_min)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: 'grouped_both',
                                uid,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax')
                            };
                        } else {
                            return {
                                type: typeId,
                                template: 'grouped',
                                uid,
                                price_min,
                                price_min_formatted: fmt(price_min)
                            };
                        }
                    }
                }

                // Configurable
                if (typeId === 'configurable') {
                    const samePriceForAll = price_min === price_max;
                    if (price_regular > 0 && price_final > 0 && price_regular !== price_final && price_min > 0) {
                        const discount_multiplier = price_final / price_regular;
                        const price_min_regular = (price_min / discount_multiplier).toFixed(2);
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same_discount_incl' : 'configurable_range_discount_incl',
                                uid,
                                price_min,
                                price_min_formatted: fmtTax(price_min),
                                price_min_regular,
                                price_min_regular_formatted: fmtTax(price_min_regular)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same_discount_both' : 'configurable_range_discount_both',
                                uid,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax'),
                                price_min_regular,
                                price_min_regular_formatted: fmt(price_min_regular),
                                price_min_regular_incl_formatted: fmtTax(price_min_regular)
                            };
                        } else {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same_discount' : 'configurable_range_discount',
                                uid,
                                price_min,
                                price_min_formatted: fmt(price_min),
                                price_min_regular,
                                price_min_regular_formatted: fmt(price_min_regular)
                            };
                        }
                    } else if (price_min > 0) {
                        if (taxDisplayMode === 'including_tax') {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same_incl' : 'configurable_range_incl',
                                uid,
                                price_min,
                                price_min_formatted: fmtTax(price_min)
                            };
                        } else if (taxDisplayMode === 'both_taxes') {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same_both' : 'configurable_range_both',
                                uid,
                                ...bothPrices(price_min, null, 'Excl. Tax', 'Incl. Tax')
                            };
                        } else {
                            return {
                                type: typeId,
                                template: samePriceForAll ? 'configurable_same' : 'configurable_range',
                                uid,
                                price_min,
                                price_min_formatted: fmt(price_min)
                            };
                        }
                    }
                }

                // Fallback: just show price_final
                if (price_final) {
                    if (taxDisplayMode === 'including_tax') {
                        return {
                            type: typeId,
                            template: 'fallback_incl',
                            uid,
                            price_final,
                            price_final_formatted: fmtTax(price_final)
                        };
                    } else if (taxDisplayMode === 'both_taxes') {
                        return {
                            type: typeId,
                            template: 'fallback_both',
                            uid,
                            ...bothPrices(price_final, null, 'Excl. Tax', 'Incl. Tax')
                        };
                    } else {
                        return {
                            type: typeId,
                            template: 'fallback',
                            uid,
                            price_final,
                            price_final_formatted: fmt(price_final)
                        };
                    }
                }
                return null;
            }
        }
    }
});
