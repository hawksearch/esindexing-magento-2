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
    'hawksearchCommon',
], function ($, _) {
    'use strict';

    var defaults = {
        excludeEvents: [],
        currencyCode: ''
    };

    /**
     * Return TrackingEvent object from initialised Vue widget
     *
     * @return {Object}
     */
    function getTrackingFromVueWidget() {
        let widget = hawksearch.getVueWidget();

        return !_.isUndefined(widget.trackEvent) ? widget.trackEvent : null;
    }

    /**
     * Make it sure to allow all known tracking events.
     * Ignore default allowed events list from TrackingEvent class
     *
     * @param {Object} trackingEvent
     * @return {void}
     */
    function allowAllTrackingEvents(trackingEvent) {
        /**
         * @param {String} item
         * @returns {String}
         */
        var mapHandler = function(item) {
            item = item === 'addToCart' ? 'add2cart' : item.toLowerCase();

            /**
             * @param {String} key
             * @returns {boolean}
             */
            var findKeyHandler = function(key) {
                return key.toLowerCase() === item;
            }

            var key = _.find(_.keys(trackingEvent.TrackEventNameMapping), findKeyHandler)

            return trackingEvent.TrackEventNameMapping[key];
        }

        let eventsToAllow = _.filter(
            _.map(Object.keys(trackingEvent.EventType), mapHandler),
            function(item) {
                return !_.contains(trackingEvent.AvailableEvents, item)
            }
        );
        trackingEvent.AvailableEvents.push(...eventsToAllow);
    }

    /**
     * Exclude some events from allowed list
     *
     * @param {Object} trackingEvent
     * @param {Array} events
     * @return {void}
     */
    function disallowTrackingEvents(trackingEvent, events) {
        allowAllTrackingEvents(trackingEvent);

        if (!_.size(events)) {
            return;
        }

        trackingEvent.AvailableEvents = _.filter(
            trackingEvent.AvailableEvents,
            function (item) {
                return !_.contains(events, item)
            }
        );
    }

    /**
     * Send 'sale' event tracking information to HawkSearch Tracking API
     *
     * @param {Object} trackingEvent
     * @param {Array} orderData Multiple orders array
     * @link https://developerdocs.hawksearch.com/docs/event-tracking-api#sale-event
     */
    function sendOrderData(trackingEvent, orderData)
    {
        if (!_.size(orderData)) {
            return;
        }

        $.each(orderData, function (index, value) {
            trackingEvent.track('sale', value);
        });
    }

    return function (config) {
        var options = [];
        $.extend(options, defaults, config);

        var trackingEvent = getTrackingFromVueWidget();

        if (trackingEvent) {
            disallowTrackingEvents(trackingEvent, options.excludeEvents);
            sendOrderData(trackingEvent, options.orderData);
            return trackingEvent;
        }

        var pendingEvents = [];
        var unavailable = false;
        hawksearch.whenVueWidgetsReady().done(function () {
            trackingEvent = getTrackingFromVueWidget();
            if (!trackingEvent) {
                unavailable = true;
                console.warn('Hawksearch event tracking is unavailable: no initialized widget has tracking.');
                pendingEvents = [];
                return;
            }

            disallowTrackingEvents(trackingEvent, options.excludeEvents);
            sendOrderData(trackingEvent, options.orderData);
            pendingEvents.forEach(function (args) {
                trackingEvent.track.apply(trackingEvent, args);
            });
            pendingEvents = [];
        }).fail(function (error) {
            unavailable = true;
            pendingEvents = [];
            console.error('Hawksearch event tracking could not initialize.', error);
        });

        return {
            track: function () {
                if (trackingEvent) {
                    return trackingEvent.track.apply(trackingEvent, arguments);
                }
                if (!unavailable) {
                    pendingEvents.push(Array.prototype.slice.call(arguments));
                } else {
                    console.warn('Hawksearch event tracking is unavailable; event was not sent.');
                }
            }
        };
    };
});
