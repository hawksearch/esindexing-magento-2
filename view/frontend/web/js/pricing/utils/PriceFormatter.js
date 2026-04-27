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
    'uiClass',
    'priceUtils',
], function(
    Class,
    priceUtils
) {
    'use strict';

    return Class.extend({

        /**
         * @param {Object} config - Configuration object with currency and locale settings
         */
        initialize: function (config) {
            this._super();
            this.config = config || {};

            return this;
        },

        /**
         * Format a price value as a currency string
         *
         * @param {number} price - The price value to format
         * @returns {string} Formatted price string
         * @public
         */
        format: function (price) {
            return priceUtils.formatPriceLocale(price, this.config)
        }
    });
});
