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
 * Bundle Product Type Handler
 * Handles pricing for bundle products with price ranges
 *
 * @module HawkSearch_EsIndexing/js/pricing/handlers/BundleProductHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    'use strict';

    return BaseProductTypeHandler.extend({
        defaults: {
            type: 'bundle',
        },

        /**
         * Process bundle product data to extract pricing information
         *
         * @param {ProductTypeData} productData - Raw product data from external service
         * @param {TaxConfiguration} taxConfig - Tax configuration
         * @returns {BundlePriceData} Processed price data
         * @public
         */
        process: function (productData, taxConfig) {
            // Validate required fields
            this._validateFields(productData, [
                'type_id', '__uid', 'price_final', 'price_min', 'price_max'
            ], true);

            // Extract prices
            var finalPrice = Number(productData.price_final);
            var regularPrice = productData.price_regular != null ? Number(productData.price_regular) : null;
            var finalPriceIncludingTax = this._calculateTaxInclusivePrices(finalPrice, taxConfig);
            var regularPriceIncludingTax = regularPrice != null
                ? this._calculateTaxInclusivePrices(regularPrice, taxConfig)
                : null;

            // Calculate discount
            var discount = this._initDiscountRate(regularPrice, finalPrice);

            // Extract rest prices
            var priceMin = Number(productData.price_min);
            var priceMax = Number(productData.price_max);
            var priceMinIncludingTax = this._calculateTaxInclusivePrices(priceMin, taxConfig);
            var priceMaxIncludingTax = this._calculateTaxInclusivePrices(priceMax, taxConfig);
            var priceMinRegular = this._extractOriginalPriceFromDiscounted(priceMin, discount);
            var priceMaxRegular = this._extractOriginalPriceFromDiscounted(priceMax, discount);
            var priceMinRegularIncludingTax = this._calculateTaxInclusivePrices(priceMinRegular, taxConfig);
            var priceMaxRegularIncludingTax = this._calculateTaxInclusivePrices(priceMaxRegular, taxConfig);

            // Build price range
            var priceRange = [
                {
                    amount: {
                        amount: priceMin,
                        formatted: this.priceFormatter.format(priceMin)
                    },
                    amountIncludingTax: priceMinIncludingTax != null ? {
                        amount: priceMinIncludingTax,
                        formatted: this.priceFormatter.format(priceMinIncludingTax)
                    } : null,
                    regularAmount: priceMinRegular != null ? {
                        amount: priceMinRegular,
                        formatted: this.priceFormatter.format(priceMinRegular)
                    } : null,
                    regularAmountIncludingTax: priceMinRegularIncludingTax != null ? {
                        amount: priceMinRegularIncludingTax,
                        formatted: this.priceFormatter.format(priceMinRegularIncludingTax)
                    } : null,
                    rangeItemType: 'from'
                },
                {
                    amount: {
                        amount: priceMax,
                        formatted: this.priceFormatter.format(priceMax)
                    },
                    amountIncludingTax: priceMaxIncludingTax != null ? {
                        amount: priceMaxIncludingTax,
                        formatted: this.priceFormatter.format(priceMaxIncludingTax)
                    } : null,
                    regularAmount: priceMaxRegular != null ? {
                        amount: priceMaxRegular,
                        formatted: this.priceFormatter.format(priceMaxRegular)
                    } : null,
                    regularAmountIncludingTax: priceMaxRegularIncludingTax != null ? {
                        amount: priceMaxRegularIncludingTax,
                        formatted: this.priceFormatter.format(priceMaxRegularIncludingTax)
                    } : null,
                    rangeItemType: 'to'
                }
            ];

            // Format result
            var result = {
                type: this.type,
                uid: String(productData.__uid),
                discount: discount,
                finalPrice: {
                    amount: finalPrice,
                    formatted: this.priceFormatter.format(finalPrice)
                },
                finalPriceIncludingTax: finalPriceIncludingTax != null ? {
                    amount: finalPriceIncludingTax,
                    formatted: this.priceFormatter.format(finalPriceIncludingTax)
                } : null,
                regularPrice: regularPrice != null ? {
                    amount: regularPrice,
                    formatted: this.priceFormatter.format(regularPrice)
                } : null,
                regularPriceIncludingTax: regularPriceIncludingTax != null ? {
                    amount: regularPriceIncludingTax,
                    formatted: this.priceFormatter.format(regularPriceIncludingTax)
                } : null,
                taxMode: taxConfig ? taxConfig.displayMode : 'excluding_tax',
                priceRange: priceRange
            };

            return result;
        }
    });
});
