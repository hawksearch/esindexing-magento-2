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
 * Price Amount Wrapper Component
 * Displays a single price amount with proper HTML structure and data attributes
 * This is a leaf component in the pricing hierarchy
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-amount-wrapper
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-amount-wrapper.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-amount-wrapper',
        template: template,

        props: {
            /**
             * Numeric price amount
             * @type {Number}
             * @required
             */
            amount: {
                type: Number,
                required: true,
                validator: function(value) {
                    return value >= 0;
                }
            },

            /**
             * Formatted price string (e.g., "$32.00")
             * @type {String}
             * @required
             */
            formattedPrice: {
                type: String,
                required: true
            },

            /**
             * Price type identifier for data attribute
             * @type {String}
             * @default 'final'
             */
            priceType: {
                type: String,
                default: 'final'
            }
        }
    };
});
