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
 * Tax calculation utility
 * Provides methods for calculating tax amounts and tax-inclusive prices
 *
 * @module HawkSearch_EsIndexing/js/pricing/utils/TaxCalculator
 */
define(['uiClass'], function(Class) {
    'use strict';

    return Class.extend({
        /**
         * Calculate price with tax included
         *
         * @param {number} price - Base price excluding tax
         * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
         * @returns {number} Price with tax included
         * @public
         */
        calculateInclusive: function (price, taxRate) {
            if (price == null || isNaN(price) || price < 0) {
                return 0;
            }
            if (taxRate == null || isNaN(taxRate) || taxRate < 0) {
                return Number(price);
            }
            return Number(price) * (1 + Number(taxRate));
        },

        /**
         * Calculate tax amount from base price
         *
         * @param {number} price - Base price excluding tax
         * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
         * @returns {number} Tax amount
         * @public
         */
        calculateTaxAmount: function (price, taxRate) {
            if (price == null || isNaN(price) || price < 0) {
                return 0;
            }
            if (taxRate == null || isNaN(taxRate) || taxRate < 0) {
                return 0;
            }
            return this.calculateInclusive(price, taxRate) - Number(price);
        },

        /**
         * Calculate base price from tax-inclusive price
         *
         * @param {number} inclusivePrice - Price including tax
         * @param {number} taxRate - Tax rate as decimal (e.g., 0.2 for 20%)
         * @returns {number} Base price excluding tax
         * @public
         */
        calculateExclusive: function (inclusivePrice, taxRate) {
            if (inclusivePrice == null || isNaN(inclusivePrice) || inclusivePrice < 0) {
                return 0;
            }
            if (taxRate == null || isNaN(taxRate) || taxRate <= 0) {
                return Number(inclusivePrice);
            }
            return Number(inclusivePrice) / (1 + Number(taxRate));
        }
    });
});
