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
 * Product Type Handler Registry
 * Manages registration and retrieval of product type handlers
 *
 * @module HawkSearch_EsIndexing/js/pricing/ProductTypeHandlerRegistry
 */
define([
    'uiClass',
    'HawkSearch_EsIndexing/js/pricing/utils/PriceFormatter',
], function(
    Class,
    PriceFormatter,
) {
    'use strict';

    return Class.extend({
        defaults: {
            handlers: {},
            fallbackHandler: null,
            priceFormatter: null
        },

    /**
         * @param {Object} config - Configuration object with price format and tax settings
     */
        initialize: function (config) {
            this._super();
            this.priceFormatter = new PriceFormatter(config?.priceFormat || {})

            return this;
        },

    /**
     * Register a handler for a product type
     *
     * @param {string} productType - Product type identifier (e.g., 'simple', 'bundle')
     * @param {BaseProductTypeHandler} handler - Handler instance
     * @returns {ProductTypeHandlerRegistry} This instance for chaining
     * @public
     */
        register: function (productType, handler) {
        if (!productType || typeof productType !== 'string') {
            throw new Error('Product type must be a non-empty string');
        }

        if (!handler || typeof handler.process !== 'function') {
            throw new Error('Handler must implement process() method');
        }

        // Inject price formatter into handler
        if (typeof handler.setPriceFormatter === 'function') {
            handler.setPriceFormatter(this.priceFormatter);
        }

        this.handlers[productType] = handler;
        return this;
        },

    /**
     * Register a fallback handler for unknown product types
     *
     * @param {BaseProductTypeHandler} handler - Fallback handler instance
     * @returns {ProductTypeHandlerRegistry} This instance for chaining
     * @public
     */
        registerFallback: function (handler) {
        if (!handler || typeof handler.process !== 'function') {
            throw new Error('Fallback handler must implement process() method');
        }

        // Inject price formatter into handler
        if (typeof handler.setPriceFormatter === 'function') {
            handler.setPriceFormatter(this.priceFormatter);
        }

        this.fallbackHandler = handler;
        return this;
        },

    /**
     * Get handler for a product type
     *
     * @param {string} productType - Product type identifier
     * @returns {BaseProductTypeHandler|null} Handler instance or null if not found
     * @public
     */
        getHandler: function (productType) {
        if (!productType) {
            return this.fallbackHandler;
        }

        // Normalize product type
        var normalizedType = String(productType).toLowerCase();

        // Check if handler exists
        if (this.handlers[normalizedType]) {
            return this.handlers[normalizedType];
        }

        // Return fallback handler if available
        return this.fallbackHandler;
        },

    /**
     * Check if a handler is registered for a product type
     *
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if handler is registered
     * @public
     */
        hasHandler: function (productType) {
        if (!productType) {
            return false;
        }

        var normalizedType = String(productType).toLowerCase();
        return this.handlers.hasOwnProperty(normalizedType);
        },

    /**
     * Get all registered product types
     *
     * @returns {Array<string>} List of registered product types
     * @public
     */
        getRegisteredTypes: function () {
        return Object.keys(this.handlers);
        }
    });
});
