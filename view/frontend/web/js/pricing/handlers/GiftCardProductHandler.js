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
 * Gift Card Product Type Handler
 * Handles pricing for gift card products
 *
 * @module HawkSearch_EsIndexing/js/pricing/handlers/GiftCardProductHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    'use strict';

    /**
     * Gift card product handler
     * @class
     * @extends BaseProductTypeHandler
     * @constructor
     */
    function GiftCardProductHandler() {
        BaseProductTypeHandler.call(this);
    }

    // Inherit from BaseProductTypeHandler
    GiftCardProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    GiftCardProductHandler.prototype.constructor = GiftCardProductHandler;

    /**
     * Check if this handler can process the given product type
     *
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    GiftCardProductHandler.prototype.canHandle = function(productType) {
        var normalizedType = String(productType).toLowerCase();
        return normalizedType === 'giftcard';
    };

    /**
     * Process gift card product data to extract pricing information
     *
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {GiftCardPriceData} Processed price data
     * @public
     */
    GiftCardProductHandler.prototype.process = function(productData, taxConfig) {
        // Validate required fields
        var validation = this._validateFields(productData, ['type_id', '__uid', 'price_final']);
        if (!validation.valid) {
            throw new Error('Invalid gift card data: missing or invalid fields - ' +
                          validation.missingFields.concat(validation.invalidFields).join(', '));
        }

        // Extract prices
        var finalPrice = Number(productData.price_final);
        var regularPrice = productData.price_regular != null ? Number(productData.price_regular) : null;
        var finalPriceIncludingTax = this._calculateTaxInclusivePrices(finalPrice, taxConfig);
        var regularPriceIncludingTax = regularPrice != null
            ? this._calculateTaxInclusivePrices(regularPrice, taxConfig)
            : null;
        var priceMin = Number(productData.price_min);
        var priceMax = Number(productData.price_max);
        var priceMinIncludingTax = this._calculateTaxInclusivePrices(priceMin, taxConfig);
        var priceMaxIncludingTax = this._calculateTaxInclusivePrices(priceMax, taxConfig);

        // Gift cards typically don't have discounts
        var discount = this._initDiscountRate(regularPrice, finalPrice);

        // Format prices
        var result = {
            type: 'giftcard',
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
            priceRange: []
        };

        return result;
    };

    return GiftCardProductHandler;
});
