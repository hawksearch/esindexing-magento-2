/**
 * Simple Product Type Handler
 * Handles pricing for simple, virtual, and downloadable products
 * 
 * @module HawkSearch_EsIndexing/js/pricing/handlers/SimpleProductHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    'use strict';

    /**
     * Simple product handler
     * @class
     * @extends BaseProductTypeHandler
     * @constructor
     */
    function SimpleProductHandler() {
        BaseProductTypeHandler.call(this);
    }

    // Inherit from BaseProductTypeHandler
    SimpleProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    SimpleProductHandler.prototype.constructor = SimpleProductHandler;

    /**
     * Check if this handler can process the given product type
     * 
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    SimpleProductHandler.prototype.canHandle = function(productType) {
        var normalizedType = String(productType).toLowerCase();
        return normalizedType === 'simple' || 
               normalizedType === 'virtual' || 
               normalizedType === 'downloadable';
    };

    /**
     * Process simple product data to extract pricing information
     * 
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {SimpleProductPriceData} Processed price data
     * @public
     */
    SimpleProductHandler.prototype.process = function(productData, taxConfig) {
        // Validate required fields
        var validation = this._validateFields(productData, ['type_id', '__uid', 'price_final']);
        if (!validation.valid) {
            throw new Error('Invalid product data: missing or invalid fields - ' + 
                          validation.missingFields.concat(validation.invalidFields).join(', '));
        }

        // Extract prices
        var finalPrice = Number(productData.price_final);
        var regularPrice = productData.price_regular != null ? 
            Number(productData.price_regular) : null;

        // Calculate discount
        var discount = this._calculateDiscount(regularPrice, finalPrice);

        // Calculate tax prices if needed
        var finalPriceIncludingTax = null;
        var regularPriceIncludingTax = null;

        if (taxConfig && taxConfig.displayMode !== 'excluding_tax') {
            var taxRate = taxConfig.taxRate || 0;
            
            if (taxConfig.priceIncludesTax) {
                // Prices already include tax
                finalPriceIncludingTax = finalPrice;
                regularPriceIncludingTax = regularPrice;
            } else {
                // Calculate tax-inclusive prices
                finalPriceIncludingTax = this.taxCalculator.calculateInclusive(finalPrice, taxRate);
                if (regularPrice != null) {
                    regularPriceIncludingTax = this.taxCalculator.calculateInclusive(regularPrice, taxRate);
                }
            }
        }

        // Format prices
        var result = {
            type: 'simple',
            uid: String(productData.__uid),
            hasDiscount: discount.hasDiscount,
            finalPrice: finalPrice,
            finalPriceFormatted: this.priceFormatter.format(finalPrice),
            finalPriceIncludingTax: finalPriceIncludingTax,
            finalPriceIncludingTaxFormatted: finalPriceIncludingTax != null ? 
                this.priceFormatter.format(finalPriceIncludingTax) : null,
            regularPrice: regularPrice,
            regularPriceFormatted: regularPrice != null ? 
                this.priceFormatter.format(regularPrice) : null,
            regularPriceIncludingTax: regularPriceIncludingTax,
            regularPriceIncludingTaxFormatted: regularPriceIncludingTax != null ? 
                this.priceFormatter.format(regularPriceIncludingTax) : null,
            taxMode: taxConfig ? taxConfig.displayMode : 'excluding_tax',
            priceRange: null, // Simple products don't have price ranges
            discountPercent: discount.discountPercent,
            discountAmount: discount.discountAmount
        };

        return result;
    };

    return SimpleProductHandler;
});
