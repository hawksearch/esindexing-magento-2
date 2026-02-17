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
            /*containerType: {
                type: String,
                required: true,
                validator: function(value) {
                    return ['special', 'regular', 'normal'].indexOf(value) !== -1;
                }
            },*/
            // Position of the label slot: 'outer', 'inner', or 'none'
            labelPosition: {
                type: String,
                default: 'none',
                validator: (value) => ['outer', 'inner', 'none'].includes(value)
            },
            // Order of label relative to price: 'before' or 'after'
            labelOrder: {
                type: String,
                default: 'before',
                validator: (value) => ['before', 'after'].includes(value)
            },


            priceContainerTag: {
                type: String,
                default: 'span'
            },
            priceContainerClass: {
                type: [String, Array, Object],
                default: 'price-container price-final_price tax weee'
            },
            wrapperElementTag: {
                type: String,
                default: null
            },
            wrapperElementClass: {
                type: [String, Array, Object],
                default: ''
            },
        },


        computed: {
            outerContainerTag: function() {
                return this.wrapperElementTag ? this.wrapperElementTag : this.priceContainerTag;
            },

            outerContainerClass: function() {
                return this.wrapperElementTag ? this.wrapperElementClass : this.priceContainerClass;
            },

            innerContainerTag: function() {
                return !this.wrapperElementTag ? this.wrapperElementTag : this.priceContainerTag;
            },

            innerContainerClass: function() {
                return !this.wrapperElementTag ? this.wrapperElementClass : this.priceContainerClass;
            },
            /**
             * Compute CSS class based on container type
             * @returns {String}
             */
            /*containerClass: function() {
                var classMap = {
                    'special': 'special-price',
                    'regular': 'old-price',
                    'normal': 'normal-price'
                };
                return classMap[this.containerType] || 'normal-price';
            }*/
        }
    };
});
