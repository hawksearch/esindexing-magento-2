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
 * Price Data Processor
 * Main processor that orchestrates product type handlers to process pricing data
 *
 * @module HawkSearch_EsIndexing/js/pricing/PriceDataProcessor
 */
define([
    'uiClass',
    'HawkSearch_EsIndexing/js/pricing/ProductTypeHandlerRegistry',
    'HawkSearch_EsIndexing/js/pricing/handlers/SimpleProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/DownloadableProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/VirtualProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/GiftCardProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/BundleProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/GroupedProductHandler',
    'HawkSearch_EsIndexing/js/pricing/handlers/ConfigurableProductHandler'
], function(
    Class,
    ProductTypeHandlerRegistry,
    SimpleProductHandler,
    DownloadableProductHandler,
    VirtualProductHandler,
    GiftCardProductHandler,
    BundleProductHandler,
    GroupedProductHandler,
    ConfigurableProductHandler
) {
    'use strict';

    return Class.extend({

    /**
     * @param {Object} config - Configuration object with price format and tax settings
     */
        initialize: function (config) {
            this._super();
        this.config = config || {};
        this.registry = new ProductTypeHandlerRegistry(this.config);
        this._initializeHandlers();

            return this;
        },

    /**
     * Initialize and register all product type handlers
     * @private
     */
        _initializeHandlers: function () {
        // Register all handlers
        this.registry.register('simple', new SimpleProductHandler());
        this.registry.register('downloadable', new DownloadableProductHandler());
        this.registry.register('virtual', new VirtualProductHandler());
        this.registry.register('giftcard', new GiftCardProductHandler());
        this.registry.register('bundle', new BundleProductHandler());
        this.registry.register('grouped', new GroupedProductHandler());
        this.registry.register('configurable', new ConfigurableProductHandler());

        // Register simple handler as fallback for unknown types
        this.registry.registerFallback(new SimpleProductHandler());
        },

    /**
     * Process product data to extract pricing information
     *
     * @param {ProductTypeData} productData - Raw product data from external service
     * @param {TaxConfiguration} taxConfig - Tax configuration
     * @returns {PriceData} Processed price data
     * @public
     */
        process: function (productData, taxConfig) {
        if (!productData || !productData.type_id) {
            throw new Error('Invalid product data: missing type_id');
        }

        // Get appropriate handler for product type
        var handler = this.registry.getHandler(productData.type_id);

        if (!handler || !handler.canHandle(productData.type_id)) {
            throw new Error('No handler found for product type: ' + productData.type_id);
        }

        // Process product data through handler
        try {
            return handler.process(productData, taxConfig);
        } catch (error) {
            console.error('Error processing product data:', error);
            throw error;
        }
        },

    /**
     * Check if a product type is supported
     *
     * @param {string} productType - Product type identifier
     * @returns {boolean} True if product type is supported
     * @public
     */
        supportsProductType: function (productType) {
        return this.registry.hasHandler(productType);
        },

    /**
     * Get list of supported product types
     *
     * @returns {Array<string>} List of supported product types
     * @public
     */
        getSupportedTypes: function () {
        return this.registry.getRegisteredTypes();
        }
    });
});
