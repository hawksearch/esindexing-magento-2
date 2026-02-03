/**
 * Price formatting utility
 * Provides methods for formatting price values according to store configuration
 * 
 * @module HawkSearch_EsIndexing/js/pricing/utils/PriceFormatter
 */
define([], function() {
    'use strict';

    /**
     * Price formatting utility
     * @class
     * @constructor
     * @param {Object} config - Configuration object with currency and locale settings
     * @param {string} [config.currencySymbol='$'] - Currency symbol to display
     * @param {string} [config.currencyPosition='before'] - Position of currency symbol ('before' or 'after')
     * @param {number} [config.decimalPlaces=2] - Number of decimal places to display
     * @param {string} [config.decimalSeparator='.'] - Character used as decimal separator
     * @param {string} [config.thousandsSeparator=','] - Character used as thousands separator
     */
    function PriceFormatter(config) {
        this.config = config || {};
        this.currencySymbol = this.config.currencySymbol || '$';
        this.currencyPosition = this.config.currencyPosition || 'before';
        this.decimalPlaces = this.config.decimalPlaces != null ? this.config.decimalPlaces : 2;
        this.decimalSeparator = this.config.decimalSeparator || '.';
        this.thousandsSeparator = this.config.thousandsSeparator || ',';
    }

    /**
     * Format a price value as a currency string
     * 
     * @param {number} price - The price value to format
     * @returns {string} Formatted price string
     * @public
     */
    PriceFormatter.prototype.format = function(price) {
        if (price == null || isNaN(price)) {
            return this.currencySymbol + '0' + this.decimalSeparator + '00';
        }

        var numericPrice = Number(price);
        var formattedNumber = this._formatNumber(numericPrice);

        if (this.currencyPosition === 'after') {
            return formattedNumber + this.currencySymbol;
        }
        
        return this.currencySymbol + formattedNumber;
    };

    /**
     * Format a number with thousands separator and decimal places
     * 
     * @param {number} number - The number to format
     * @returns {string} Formatted number string
     * @private
     */
    PriceFormatter.prototype._formatNumber = function(number) {
        var fixedNumber = number.toFixed(this.decimalPlaces);
        var parts = fixedNumber.split('.');
        
        // Add thousands separator
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.thousandsSeparator);
        
        return parts.join(this.decimalSeparator);
    };

    /**
     * Parse a formatted price string to a numeric value
     * 
     * @param {string} formattedPrice - The formatted price string
     * @returns {number} Numeric price value
     * @public
     */
    PriceFormatter.prototype.parse = function(formattedPrice) {
        if (!formattedPrice || typeof formattedPrice !== 'string') {
            return 0;
        }

        // Remove currency symbol and spaces
        var cleaned = formattedPrice
            .replace(this.currencySymbol, '')
            .replace(/\s/g, '')
            .replace(new RegExp('\\' + this.thousandsSeparator, 'g'), '')
            .replace(new RegExp('\\' + this.decimalSeparator), '.');

        var parsed = parseFloat(cleaned);
        return isNaN(parsed) ? 0 : parsed;
    };

    return PriceFormatter;
});
