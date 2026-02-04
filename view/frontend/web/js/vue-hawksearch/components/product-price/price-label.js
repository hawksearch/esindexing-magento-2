/**
 * Price Label Component
 * Displays price labels with configurable positioning (outer/inner)
 * Handles labels like "Special Price", "Regular Price", "As low as", etc.
 * 
 * @module HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price/price-label
 */
define([
    'hawksearchVueSDK',
    'text!HawkSearch_EsIndexing/template/vue-hawksearch/components/product-price/price-label.html'
], function(HawksearchVue, template) {
    'use strict';

    return HawksearchVue.component('price-label', {
        template: template,
        
        props: {
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
             * Whether label should be positioned outside (before) the price container
             * @type {Boolean}
             * @default false
             */
            outerLabel: {
                type: Boolean,
                default: false
            },
            
            /**
             * CSS class for the label
             * @type {String}
             * @default 'price-label'
             */
            labelClass: {
                type: String,
                default: 'price-label'
            }
        }
    });
});
