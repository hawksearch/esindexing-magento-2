/**
 * Abstract base class for product type handlers
 * Provides common functionality and defines interface that all handlers must implement
 * 
 * @module HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler
 */
define([
    'HawkSearch_EsIndexing/js/pricing/utils/TaxCalculator',
    'HawkSearch_EsIndexing/js/pricing/utils/PriceFormatter'
], function(TaxCalculator, PriceFormatter) {
    'use strict';

    /**
     * Base product type handler
     * @class
     * @constructor
     * @abstract
     */
    function BaseProductTypeHandler() {
        this.taxCalculator = new TaxCalculator();
        this.priceFormatter = null; // Will be injected by registry
    }

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
    BaseProductTypeHandler.prototype.process = function(productData, taxConfig) {
        throw new Error('process() must be implemented by subclass');
    };

    /**
     * Check if this handler can process the given product type
     * 
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if this handler can process the type
     * @public
     */
    BaseProductTypeHandler.prototype.canHandle = function(productType) {
        throw new Error('canHandle() must be implemented by subclass');
    };

    /**
     * Calculate discount information
     * 
     * @param {number} regularPrice - Regular price
     * @param {number} finalPrice - Final price after discount
     * @returns {Object} Discount information
     * @protected
     */
    BaseProductTypeHandler.prototype._calculateDiscount = function(regularPrice, finalPrice) {
        var hasDiscount = regularPrice != null && 
                         finalPrice != null && 
                         regularPrice > finalPrice;
        
        var discountAmount = hasDiscount ? (regularPrice - finalPrice) : 0;
        var discountPercent = hasDiscount ? 
            ((regularPrice - finalPrice) / regularPrice * 100).toFixed(0) : 
            0;

        return {
            hasDiscount: hasDiscount,
            discountAmount: discountAmount,
            discountPercent: discountPercent
        };
    };

    /**
     * Validate required fields in product data
     * 
     * @param {Object} productData - Product data to validate
     * @param {Array<string>} requiredFields - List of required field names
     * @returns {Object} Validation result
     * @protected
     */
    BaseProductTypeHandler.prototype._validateFields = function(productData, requiredFields) {
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
    };

    /**
     * Set price formatter instance
     * 
     * @param {PriceFormatter} formatter - Price formatter instance
     * @public
     */
    BaseProductTypeHandler.prototype.setPriceFormatter = function(formatter) {
        this.priceFormatter = formatter;
    };

    return BaseProductTypeHandler;
});
