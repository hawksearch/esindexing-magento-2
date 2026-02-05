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
 * Price Container Component
 * Wraps price amounts with appropriate CSS classes
 * Handles special-price, old-price, and normal-price containers
 *
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-container
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-container.html'
], function(HawksearchVue, template) {
    'use strict';

    return {
        name: 'price-container',
        template: template,

        props: {
            /**
             * Container type (determines CSS class)
             * @type {String}
             * @required
             * @values 'special'|'regular'|'normal'
             */
            containerType: {
                type: String,
                required: true,
                validator: function(value) {
                    return ['special', 'regular', 'normal'].indexOf(value) !== -1;
                }
            }
        },

        computed: {
            /**
             * Compute CSS class based on container type
             * @returns {String}
             */
            containerClass: function() {
                var classMap = {
                    'special': 'special-price',
                    'regular': 'old-price',
                    'normal': 'normal-price'
                };
                return classMap[this.containerType] || 'normal-price';
            }
        }
    };
});
