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
 * Displays a price amount wrapper element with proper HTML structure and data attributes
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
             * Price amount object
             * @type {PriceAmount|null}
             * @required
             */
            amount: {
                type: Object
            },

            /**
             * CSS class for inner amount element
             * @type {String|Array|Object}
             */
            amountElementClass: {
                type: [String, Array, Object],
                default: ''
            },

            /**
             * Price type identifier for data attribute
             * @type {String}
             * @default null
             */
            priceType: {
                type: String,
                default: null
            },

            /**
             * Label text to display
             * @type {String}
             * @default null
             */
            labelText: {
                type: String,
                default: null
            },

            /**
             * Root element id
             * @type {String}
             * @default null
             */
            elementId: {
                type: String,
                default: null
            }
        }
    };
});
