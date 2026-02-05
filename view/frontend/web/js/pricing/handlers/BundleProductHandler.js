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

    /**
     * Bundle product handler
     * @class
     * @extends BaseProductTypeHandler
     * @constructor
     */
    function BundleProductHandler() {
        BaseProductTypeHandler.call(this);
    }

    // Inherit from BaseProductTypeHandler
    BundleProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    BundleProductHandler.prototype.constructor = BundleProductHandler;

    /**
     * Check if this handler can process the given product type
     *
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    BundleProductHandler.prototype.canHandle = function(productType) {
        var normalizedType = String(productType).toLowerCase();
        return normalizedType === 'bundle';
    };

    /**
     * Process bundle product data to extract pricing information
     *
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {BundlePriceData} Processed price data
     * @public
     */
    BundleProductHandler.prototype.process = function(productData, taxConfig) {
        // Validate required fields
        var validation = this._validateFields(productData, [
            'type_id', '__uid', 'price_final', 'price_min', 'price_max'
        ]);
        if (!validation.valid) {
            throw new Error('Invalid bundle product data: missing or invalid fields - ' +
                          validation.missingFields.concat(validation.invalidFields).join(', '));
        }

        // Extract prices
        var priceMin = Number(productData.price_min);
        var priceMax = Number(productData.price_max);
        var hasRange = priceMin !== priceMax;

        // Calculate tax prices if needed
        var priceMinIncludingTax = null;
        var priceMaxIncludingTax = null;

        if (taxConfig && taxConfig.displayMode !== 'excluding_tax') {
            var taxRate = taxConfig.taxRate || 0;

            if (taxConfig.priceIncludesTax) {
                priceMinIncludingTax = priceMin;
                priceMaxIncludingTax = priceMax;
            } else {
                priceMinIncludingTax = this.taxCalculator.calculateInclusive(priceMin, taxRate);
                priceMaxIncludingTax = this.taxCalculator.calculateInclusive(priceMax, taxRate);
            }
        }

        // Build price range
        var priceRange = {
            minimum: {
                amount: priceMin,
                formatted: this.priceFormatter.format(priceMin),
                type: 'minPrice'
            },
            maximum: {
                amount: priceMax,
                formatted: this.priceFormatter.format(priceMax),
                type: 'maxPrice'
            },
            minimumIncludingTax: priceMinIncludingTax != null ? {
                amount: priceMinIncludingTax,
                formatted: this.priceFormatter.format(priceMinIncludingTax),
                type: 'minPriceIncludingTax'
            } : null,
            maximumIncludingTax: priceMaxIncludingTax != null ? {
                amount: priceMaxIncludingTax,
                formatted: this.priceFormatter.format(priceMaxIncludingTax),
                type: 'maxPriceIncludingTax'
            } : null,
            hasRange: hasRange,
            rangeLabel: 'From'
        };

        // Format result
        var result = {
            type: 'bundle',
            uid: String(productData.__uid),
            hasDiscount: false, // Bundles don't have traditional discounts
            finalPrice: priceMin, // Use minimum price as final price
            finalPriceFormatted: this.priceFormatter.format(priceMin),
            finalPriceIncludingTax: priceMinIncludingTax,
            finalPriceIncludingTaxFormatted: priceMinIncludingTax != null ?
                this.priceFormatter.format(priceMinIncludingTax) : null,
            regularPrice: null,
            regularPriceFormatted: null,
            regularPriceIncludingTax: null,
            regularPriceIncludingTaxFormatted: null,
            taxMode: taxConfig ? taxConfig.displayMode : 'excluding_tax',
            priceRange: priceRange,
            discountPercent: 0,
            discountAmount: 0
        };

        return result;
    };

    return BundleProductHandler;
});
