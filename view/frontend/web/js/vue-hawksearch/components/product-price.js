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

    function formatPrice(value, priceFormat) {
        if (value == null || isNaN(value)) return '';
        // Simple formatting, can be replaced with priceUtils if available
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

                // Helper for formatted price
                const fmt = v => formatPrice(v, priceFormat);

                // Simple/Virtual/Downloadable
                if (["simple", "virtual", "downloadable"].includes(typeId)) {
                    if (price_regular && price_final && price_regular !== price_final) {
                        // Discounted
                        return {
                            type: typeId,
                            template: 'discount',
                            uid,
                            price_final,
                            price_regular,
                            price_final_formatted: fmt(price_final),
                            price_regular_formatted: fmt(price_regular)
                        };
                    } else {
                        // No discount
                        return {
                            type: typeId,
                            template: 'regular',
                            uid,
                            price_final,
                            price_final_formatted: fmt(price_final)
                        };
                    }
                }

                // Giftcard
                if (typeId === 'giftcard') {
                    if (price_final !== null && price_min !== null && price_max == null) {
                        // Heuristic: always show 'From'
                        return {
                            type: typeId,
                            template: 'giftcard',
                            uid,
                            price_min,
                            price_min_formatted: fmt(price_min)
                        };
                    }
                }

                // Bundle
                if (typeId === 'bundle') {
                    // Discount logic
                    if (price_regular > 0 && price_final > 0 && price_regular !== price_final && price_min > 0 && price_max > 0) {
                        const discount_multiplier = price_final / price_regular;
                        const price_min_regular = (price_min / discount_multiplier).toFixed(2);
                        const price_max_regular = (price_max / discount_multiplier).toFixed(2);
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
                    } else if (price_min > 0 && price_max > 0) {
                        // Fallback: just show min/max
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

                // Grouped
                if (typeId === 'grouped') {
                    if (price_min > 0) {
                        return {
                            type: typeId,
                            template: 'grouped',
                            uid,
                            price_min,
                            price_min_formatted: fmt(price_min)
                        };
                    }
                }

                // Configurable
                if (typeId === 'configurable') {
                    const samePriceForAll = price_min === price_max;
                    if (price_regular > 0 && price_final > 0 && price_regular !== price_final && price_min > 0) {
                        const discount_multiplier = price_final / price_regular;
                        const price_min_regular = (price_min / discount_multiplier).toFixed(2);
                        return {
                            type: typeId,
                            template: samePriceForAll ? 'configurable_same_discount' : 'configurable_range_discount',
                            uid,
                            price_min,
                            price_min_formatted: fmt(price_min),
                            price_min_regular,
                            price_min_regular_formatted: fmt(price_min_regular)
                        };
                    } else if (price_min > 0) {
                        return {
                            type: typeId,
                            template: samePriceForAll ? 'configurable_same' : 'configurable_range',
                            uid,
                            price_min,
                            price_min_formatted: fmt(price_min)
                        };
                    }
                }

                // Fallback: just show price_final
                if (price_final) {
                    return {
                        type: typeId,
                        template: 'fallback',
                        uid,
                        price_final,
                        price_final_formatted: fmt(price_final)
                    };
                }
                return null;
            }
        }
    }
});
