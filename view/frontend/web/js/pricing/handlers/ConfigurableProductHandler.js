/**
 * Configurable Product Type Handler
 * Handles pricing for configurable products with price ranges
 * 
 * @module HawkSearch_EsIndexing/js/pricing/handlers/ConfigurableProductHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    'use strict';

    /**
     * Configurable product handler
     * @class
     * @extends BaseProductTypeHandler
     * @constructor
     */
    function ConfigurableProductHandler() {
        BaseProductTypeHandler.call(this);
    }

    // Inherit from BaseProductTypeHandler
    ConfigurableProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    ConfigurableProductHandler.prototype.constructor = ConfigurableProductHandler;

    /**
     * Check if this handler can process the given product type
     * 
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    ConfigurableProductHandler.prototype.canHandle = function(productType) {
        var normalizedType = String(productType).toLowerCase();
        return normalizedType === 'configurable';
    };

    /**
     * Process configurable product data to extract pricing information
     * 
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {ConfigurablePriceData} Processed price data
     * @public
     */
    ConfigurableProductHandler.prototype.process = function(productData, taxConfig) {
        // Validate required fields
        var validation = this._validateFields(productData, ['type_id', '__uid', 'price_final']);
        if (!validation.valid) {
            throw new Error('Invalid configurable product data: missing or invalid fields - ' + 
                          validation.missingFields.concat(validation.invalidFields).join(', '));
        }

        // Check if product has price range
        var hasMinMax = productData.price_min != null && productData.price_max != null;
        var priceRange = null;

        if (hasMinMax) {
            // Product has price range
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

            priceRange = {
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
                rangeLabel: 'As low as'
            };
        }

        // Extract single price data
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
                finalPriceIncludingTax = finalPrice;
                regularPriceIncludingTax = regularPrice;
            } else {
                finalPriceIncludingTax = this.taxCalculator.calculateInclusive(finalPrice, taxRate);
                if (regularPrice != null) {
                    regularPriceIncludingTax = this.taxCalculator.calculateInclusive(regularPrice, taxRate);
                }
            }
        }

        // Format result
        var result = {
            type: 'configurable',
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
            priceRange: priceRange,
            discountPercent: discount.discountPercent,
            discountAmount: discount.discountAmount
        };

        return result;
    };

    return ConfigurableProductHandler;
});
