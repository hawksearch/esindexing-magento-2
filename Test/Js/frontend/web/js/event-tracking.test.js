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

define([
    'HawkSearch_EsIndexing/js/event-tracking'
], function (createTracker) {
    'use strict';

    var previousHawksearch;
    var previousWarn;
    var previousError;
    var calls;
    var logs;

    function setup(initialWidget) {
        var widget = initialWidget || {};
        var done;
        var fail;
        var status = 'pending';
        var tracking = {
            EventType: {},
            TrackEventNameMapping: {},
            AvailableEvents: [],
            track: function () {
                expect(this).toBe(tracking);
                calls.push(Array.prototype.slice.call(arguments));
            }
        };
        var promise = {
            done: function (callback) {
                done = callback;
                if (status === 'resolved') {
                    callback();
                }
                return this;
            },
            fail: function (callback) {
                fail = callback;
                if (status === 'rejected') {
                    callback(new Error('not initialized'));
                }
                return this;
            }
        };

        window.hawksearch = {
            getVueWidget: function () {
                return widget;
            },
            whenVueWidgetsReady: function () {
                return promise;
            }
        };

        return {
            tracking: tracking,
            calls: calls,
            resolve: function (withWidget) {
                widget = withWidget === false ? {} : {trackEvent: tracking};
                status = 'resolved';
                if (done) {
                    done();
                }
            },
            reject: function (error) {
                status = 'rejected';
                if (fail) {
                    fail(error);
                }
            }
        };
    }

    describe('HawkSearch_EsIndexing/js/event-tracking', function () {
        beforeEach(function () {
            previousHawksearch = window.hawksearch;
            previousWarn = console.warn;
            previousError = console.error;
            calls = [];
            logs = [];
            spyOn(console, 'warn').and.callFake(function () {
                logs.push(Array.prototype.slice.call(arguments));
            });
            spyOn(console, 'error').and.callFake(function () {
                logs.push(Array.prototype.slice.call(arguments));
            });
        });

        afterEach(function () {
            if (previousHawksearch === undefined) {
                delete window.hawksearch;
            } else {
                window.hawksearch = previousHawksearch;
            }
            console.warn = previousWarn;
            console.error = previousError;
        });

        it('queues tracking calls and sales until the widget is ready', function () {
            var state = setup();
            var tracker = createTracker({orderData: [{orderId: 1}]});

            tracker.track('add2cart', {uniqueId: '1'});
            expect(calls.length).toBe(0);
            state.resolve();
            tracker.track('add2cart', {uniqueId: '2'});

            expect(calls).toEqual([
                ['sale', {orderId: 1}],
                ['add2cart', {uniqueId: '1'}],
                ['add2cart', {uniqueId: '2'}]
            ]);
        });

        it('initializes immediately when a widget is already available', function () {
            var state = setup();

            state.resolve();
            expect(createTracker()).toBe(state.tracking);
        });

        it('reports unavailable tracking when readiness fails or no widget exists', function () {
            [false, true].forEach(function (rejectReadiness) {
                var state = setup();
                var tracker = createTracker();
                var previousLogCount = logs.length;

                tracker.track('add2cart', {});
                if (rejectReadiness) {
                    state.reject(new Error('not initialized'));
                } else {
                    state.resolve(false);
                }
                tracker.track('add2cart', {});

                expect(state.calls.length).toBe(0);
                expect(logs.length - previousLogCount).toBe(2);
            });
        });
    });
});
