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
 * Abstract base class for product type handlers
 * Provides common functionality and defines interface that all handlers must implement
 *
 * @module HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler
 */
define([
    'uiClass',
    'HawkSearch_EsIndexing/js/pricing/utils/TaxCalculator',
    'HawkSearch_EsIndexing/js/pricing/utils/PriceFormatter'
], function(Class, TaxCalculator, PriceFormatter) {
    'use strict';

    return Class.extend({
        defaults: {
            taxCalculator: new TaxCalculator(),
            priceFormatter: null, // Will be injected by registry
            type: null, // Will be set by concrete handler
        },

    /**
     * Process product data to extract pricing information
     * Must be implemented by subclasses
     *
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {Object} Processed price data
     * @abstract
     * @public
     */
        process: function (productData, taxConfig) {
        throw new Error('process() must be implemented by subclass');
        },

    /**
     * Check if this handler can process the given product type
     *
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
        canHandle: function (productType) {
            var normalizedType = String(productType).toLowerCase();
            return normalizedType === this.type;
        },

    /**
     * @param {number|null} regularPrice - Regular price
     * @param {number|null} finalPrice - Final price after discount
     * @returns {DiscountData} Discount information
     * @protected
     */
        _initDiscountRate: function (regularPrice, finalPrice) {
        var hasDiscount =
            regularPrice != null && finalPrice != null && regularPrice > finalPrice;

        var discountRate = hasDiscount
            ? (regularPrice - finalPrice) / regularPrice
            : 0;

        return {
            hasDiscount: hasDiscount,
            discountRate: discountRate
        };
        },

    /**
     * @param {number} price - The original price
     * @param {DiscountData} discountData - Discount information (rate as decimal)
     * @returns {number} Discounted price
     * @protected
     */
        _applyDiscountToPrice: function (price, discountData) {
        if (!discountData || !discountData.hasDiscount || !discountData.discountRate) {
            return price;
        }
        return price * (1 - discountData.discountRate);
        },

    /**
     * Extract original price from discounted price and DiscountData
     * (Inverse of _applyDiscountToPrice)
     *
     * @param {number} discountedPrice - The discounted price
     * @param {DiscountData} discountData - Discount information (rate as decimal)
     * @returns {number} Original price before discount
     * @protected
     */
        _extractOriginalPriceFromDiscounted: function(discountedPrice, discountData) {
        if (!discountData || !discountData.hasDiscount || !discountData.discountRate) {
            return discountedPrice;
        }
        return discountedPrice / (1 - discountData.discountRate);
        },

    /**
     * Validate required fields in product data
     *
     * @param {Object} productData - Product data to validate
     * @param {Array<string>} requiredFields - List of required field names
     * @returns {Object} Validation result
     * @protected
     */
        _validateFields: function (productData, requiredFields) {
        var missingFields = [];
        var invalidFields = [];

        for (var i = 0; i < requiredFields.length; i++) {
            var field = requiredFields[i];
            if (productData[field] == null) {
                missingFields.push(field);
            } else if (field.indexOf('price') >= 0 && isNaN(productData[field])) {
                invalidFields.push(field);
            }
        }

        return {
            valid: missingFields.length === 0 && invalidFields.length === 0,
            missingFields: missingFields,
            invalidFields: invalidFields
        };
        },

    /**
     * @param {number} price - Price (excluding tax)
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {number} Price including tax
     * @protected
     */
        _calculateTaxInclusivePrices: function(price, taxConfig) {
        var taxRate = taxConfig.taxRate || 0;
        var priceIncludingTax = taxConfig.priceIncludesTax
            ? price
            : this.taxCalculator.calculateInclusive(price, taxRate);

        return priceIncludingTax;
        },

    /**
     * @param {PriceFormatter} formatter - Price formatter instance
     * @public
     */
        setPriceFormatter: function (formatter) {
        this.priceFormatter = formatter;
        }
    });
});
