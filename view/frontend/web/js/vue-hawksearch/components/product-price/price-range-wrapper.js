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
 * Price Range Wrapper Component
 * Handles bundle price ranges (from/to) and grouped product containers
 * Provides different wrappers based on product type and range presence
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-range-wrapper
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-range-wrapper.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-range-wrapper',
        template: template,

        props: {
            /**
             * Whether product has a price range
             * @type {Boolean}
             * @default false
             */
            hasRange: {
                type: Boolean,
                default: false
            },

            /**
             * Range display type
             * @type {String}
             * @default null
             * @values 'from'|'to'|'grouped'|null
             */
            rangeType: {
                type: String,
                default: null,
                validator: function(value) {
                    return value === null || ['from', 'to', 'grouped'].indexOf(value) !== -1;
                }
            }
        }
    };
});
