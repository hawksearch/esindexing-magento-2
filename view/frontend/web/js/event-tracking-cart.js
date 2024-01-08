/**
 * Copyright (c) 2023 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    './event-tracking',
    'mage/utils/template',
    'mage/cookies',
    'jquery/ui'
], function ($, _, customerData, eventTracking, mageTemplate) {
    'use strict';

    $.widget('mage.hawksearchCartTracking', {
        options: {
            currencyCode: '',
            cookieAddToCart: '',
            cookieRemoveFromCart: '',
            productIdTemplate: '',
            eventQueue: [],
            cartItems: [],
            events: {
                EVENT_ADD_TO_CART: 'ajax:addToCart',
                EVENT_REMOVE_FROM_CART: 'ajax:removeFromCart'
            },
            eventHandlers: []
        },

        /**
         *
         * @private
         */
        _create: function () {
            this.eventTracking = new eventTracking();
            this._initHandlers()
            this._initListeners();
            this._initCartItems();
            this._processCookies();
        },

        /**
         * Init event handler callbacks
         *
         * @private
         */
        _initHandlers: function() {
            this.options.eventHandlers[this.options.events.EVENT_ADD_TO_CART] = this.handleAddToCart.bind(this);
            this.options.eventHandlers[this.options.events.EVENT_REMOVE_FROM_CART] = this.handleRemoveFromCart.bind(this);
        },

        /**
         * add2cart tracking event handler
         * @param item
         */
        handleAddToCart: function (item) {
            item = $.extend(item, {currency: this.options.currencyCode});
            this.eventTracking.track(
                'add2cart',
                item
            );
        },

        /**
         * Handle removing items from cart
         * @todo Add API implementation
         * @param cartItem
         */
        handleRemoveFromCart: function (cartItem) {

        },

        /**
         * Init cart event listeners
         * @private
         */
        _initListeners: function() {
            $(document)
                .on(
                    this.options.events.EVENT_ADD_TO_CART,
                    function (event, eventData) {
                        this._pushEventToQueue.call(this, this.options.events.EVENT_ADD_TO_CART, eventData.productInfo);
                    }.bind(this)

                )
                .on(
                    this.options.events.EVENT_REMOVE_FROM_CART,
                    function (event, eventData) {
                        this._pushEventToQueue.call(this, this.options.events.EVENT_REMOVE_FROM_CART, eventData.productInfo);
                    }.bind(this)
                );

            customerData.get('cart').subscribe(function (data) {
                if (this.options.eventQueue.length) {
                    this.processEventQueue();
                }

                this.options.cartItems = (data.items || []).slice();
            }.bind(this));
        },

        /**
         * Init cart items
         */
        _initCartItems: function () {
            this.options.cartItems = (customerData.get('cart')().items || []).slice();
        },

        /**
         * Process cart cookies
         * @private
         */
        _processCookies: function () {
            var addProducts,
                removeProducts;

            if ($.mage.cookies.get(this.options.cookieAddToCart)) {
                addProducts = decodeURIComponent($.mage.cookies.get(this.options.cookieAddToCart));
                addProducts = JSON.parse(addProducts);
                this._clearCookie(this.options.cookieAddToCart);
                addProducts.forEach(function (item, index) {
                    this.handleAddToCart(item);
                }.bind(this));

            }

            if ($.mage.cookies.get(this.options.cookieRemoveFromCart)) {
                removeProducts = decodeURIComponent($.mage.cookies.get(this.options.cookieRemoveFromCart));
                removeProducts = JSON.parse(removeProducts);
                this._clearCookie(this.options.cookieRemoveFromCart);
                removeProducts.forEach(function (item, index) {
                    this.handleRemoveFromCart(item);
                }.bind(this));
            }
        },

        /**
         * Push event to the queue.
         * An event from the queue is triggered after updating the cart data.
         *
         * @param  {String} type Event type
         * @param {Array} productInfo  Product info objects array
         */
        _pushEventToQueue: function(type, productInfo) {
            this.options.eventQueue.push({
                type: type,
                productInfo: productInfo
            });
        },

        /**
         * Process event queue and execute events
         */
        processEventQueue: function() {
            let productData;
            this.options.eventQueue.forEach(function (item, index) {
                if (typeof item.productInfo === 'undefined') {
                    return;
                }

                item.productInfo.forEach(function (productInfo) {
                    productData = this._findProduct(productInfo);

                    if (!_.isUndefined(productData['product_id']) && parseInt(productData.qty, 10) > 0) {
                        this.options.eventHandlers[item.type](this._convertItemProduct(productData));
                    }
                }.bind(this));

                this.options.eventQueue.splice(index, 1);
            }.bind(this));
        },

        /**
         * Find product in cart
         *
         * @param {Object} productInfo Product info
         * @return {Object} Product data object
         */
        _findProduct: function (productInfo) {
            var searchCriteria,
                productOptionValues = productInfo.optionValues || [];

            /**
             * Cart item search criteria.
             *
             * @param {Object} cartItem
             * @return {Boolean}
             */
            searchCriteria = function(cartItem) {
                if (cartItem['product_id'] !== productInfo.id) {
                    return false;
                }

                if (productOptionValues.length === 0) {
                    return true;
                }

                let index = 0;
                while (index < cartItem.options.length) {
                    if (productOptionValues.indexOf(cartItem.options[index]['option_value']) === -1) {
                        return false;
                    }
                    index++;
                }

                return true;
            };

            let itemFromCart = _.find(customerData.get('cart')().items, searchCriteria);
            let itemFromLocal = _.find(this.options.cartItems, searchCriteria);

            if (itemFromCart && itemFromLocal) {
                return _.extend({}, itemFromCart, {
                    qty: itemFromCart.qty - itemFromLocal.qty
                });
            }

            return itemFromCart || itemFromLocal || {};
        },

        /**
         * Convert cart item product data to event tracking format
         *
         * @param {Object} productData Product data object
         * @private
         */
        _convertItemProduct: function (productData) {
            return {
                uniqueId: mageTemplate.template(this.options.productIdTemplate, {id: productData['product_id']}),
                price: productData['product_price_calculated'],
                quantity: productData['qty']
            }
        },

        /**
         * Remove cookie by name
         *
         * @param {string} name
         * @private
         */
        _clearCookie: function (name) {
            $.mage.cookies.set(name, '', {
                domain:'',
                expires: new Date('Jan 01 1970 00:00:01 GMT')
            });
        }
    });

    return $.mage.hawksearchCartTracking;
});
