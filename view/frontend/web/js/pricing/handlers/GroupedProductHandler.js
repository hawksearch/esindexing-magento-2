/**
 * Grouped Product Type Handler
 * Handles pricing for grouped products with starting price display
 * 
 * @module HawkSearch_EsIndexing/js/pricing/handlers/GroupedProductHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    'use strict';

    /**
     * Grouped product handler
     * @class
     * @extends BaseProductTypeHandler
     * @constructor
     */
    function GroupedProductHandler() {
        BaseProductTypeHandler.call(this);
    }

    // Inherit from BaseProductTypeHandler
    GroupedProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    GroupedProductHandler.prototype.constructor = GroupedProductHandler;

    /**
     * Check if this handler can process the given product type
     * 
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    GroupedProductHandler.prototype.canHandle = function(productType) {
        var normalizedType = String(productType).toLowerCase();
        return normalizedType === 'grouped';
    };

    /**
     * Process grouped product data to extract pricing information
     * 
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {GroupedPriceData} Processed price data
     * @public
     */
    GroupedProductHandler.prototype.process = function(productData, taxConfig) {
        // Validate required fields
        var validation = this._validateFields(productData, [
            'type_id', '__uid', 'price_final'
        ]);
        if (!validation.valid) {
            throw new Error('Invalid grouped product data: missing or invalid fields - ' + 
                          validation.missingFields.concat(validation.invalidFields).join(', '));
        }

        // Grouped products show starting/minimum price
        var startingPrice = Number(productData.price_final);

        // Calculate tax prices if needed
        var startingPriceIncludingTax = null;

        if (taxConfig && taxConfig.displayMode !== 'excluding_tax') {
            var taxRate = taxConfig.taxRate || 0;
            
            if (taxConfig.priceIncludesTax) {
                startingPriceIncludingTax = startingPrice;
            } else {
                startingPriceIncludingTax = this.taxCalculator.calculateInclusive(startingPrice, taxRate);
            }
        }

        // Build price range (grouped products show "Starting at" with single price)
        var priceRange = {
            minimum: {
                amount: startingPrice,
                formatted: this.priceFormatter.format(startingPrice),
                type: 'startingPrice'
            },
            maximum: null, // Grouped products don't show maximum
            minimumIncludingTax: startingPriceIncludingTax != null ? {
                amount: startingPriceIncludingTax,
                formatted: this.priceFormatter.format(startingPriceIncludingTax),
                type: 'startingPriceIncludingTax'
            } : null,
            maximumIncludingTax: null,
            hasRange: false,
            rangeLabel: 'Starting at'
        };

        // Format result
        var result = {
            type: 'grouped',
            uid: String(productData.__uid),
            hasDiscount: false,
            finalPrice: startingPrice,
            finalPriceFormatted: this.priceFormatter.format(startingPrice),
            finalPriceIncludingTax: startingPriceIncludingTax,
            finalPriceIncludingTaxFormatted: startingPriceIncludingTax != null ? 
                this.priceFormatter.format(startingPriceIncludingTax) : null,
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

    return GroupedProductHandler;
});
