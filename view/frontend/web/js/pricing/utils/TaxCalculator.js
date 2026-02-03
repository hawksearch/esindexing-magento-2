/**
 * Tax calculation utility
 * Provides methods for calculating tax amounts and tax-inclusive prices
 * 
 * @module HawkSearch_EsIndexing/js/pricing/utils/TaxCalculator
 */
define([], function() {
    'use strict';

    /**
     * Tax calculation utility
     * @class
     * @constructor
     */
    function TaxCalculator() {}

    /**
     * Calculate price with tax included
     * 
     * @param {number} price - Base price excluding tax
     * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
     * @returns {number} Price with tax included
     * @public
     */
    TaxCalculator.prototype.calculateInclusive = function(price, taxRate) {
        if (price == null || isNaN(price) || price < 0) {
            return 0;
        }
        if (taxRate == null || isNaN(taxRate) || taxRate < 0) {
            return Number(price);
        }
        return Number(price) * (1 + Number(taxRate));
    };

    /**
     * Calculate tax amount from base price
     * 
     * @param {number} price - Base price excluding tax
     * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
     * @returns {number} Tax amount
     * @public
     */
    TaxCalculator.prototype.calculateTaxAmount = function(price, taxRate) {
        if (price == null || isNaN(price) || price < 0) {
            return 0;
        }
        if (taxRate == null || isNaN(taxRate) || taxRate < 0) {
            return 0;
        }
        return this.calculateInclusive(price, taxRate) - Number(price);
    };

    /**
     * Calculate base price from tax-inclusive price
     * 
     * @param {number} inclusivePrice - Price including tax
     * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
     * @returns {number} Base price excluding tax
     * @public
     */
    TaxCalculator.prototype.calculateExclusive = function(inclusivePrice, taxRate) {
        if (inclusivePrice == null || isNaN(inclusivePrice) || inclusivePrice < 0) {
            return 0;
        }
        if (taxRate == null || isNaN(taxRate) || taxRate <= 0) {
            return Number(inclusivePrice);
        }
        return Number(inclusivePrice) / (1 + Number(taxRate));
    };

    return TaxCalculator;
});
