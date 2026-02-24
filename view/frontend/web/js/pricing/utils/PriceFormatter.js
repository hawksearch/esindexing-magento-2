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
 * Price formatting utility
 * Provides methods for formatting price values according to store configuration
 *
 * @module HawkSearch_EsIndexing/js/pricing/utils/PriceFormatter
 */
define([
    'priceUtils',
], function(priceUtils) {
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
        return priceUtils.formatPriceLocale(price, this.config)
    };

    return PriceFormatter;
});
