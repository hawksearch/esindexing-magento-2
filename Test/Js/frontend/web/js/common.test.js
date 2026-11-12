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
    'require',
    'jquery',
    'hawksearchVueEvents',
    'mage/url',
    'hawksearchVueSDK'
], function (require, $, Events, urlBuilder) {
    'use strict';

    var moduleName = 'HawkSearch_EsIndexing/js/common';
    var previousGlobals;
    var fixtures = [];
    var created;
    var subscriptions;
    var eventCalls;
    var errors;
    var getJSONSpy;

    function makeRequest() {
        var resolve;
        var reject;
        var request = {
            done: function (callback) {
                resolve = callback;
                return this;
            },
            fail: function (callback) {
                reject = callback;
                return this;
            },
            resolve: function (value) {
                if (resolve) {
                    resolve(value);
                }
            },
            reject: function (error) {
                if (reject) {
                    reject(error);
                }
            }
        };

        return request;
    }

    function setup(componentNames, publicConfig, options) {
        var fixture = document.createElement('div');
        var request = makeRequest();
        var load;

        fixtures.forEach(function (existingFixture) {
            existingFixture.remove();
        });
        fixtures = [];
        created = [];
        subscriptions = [];
        eventCalls = [];
        errors = [];

        componentNames = componentNames || ['searchbox', 'results', 'facets'];
        publicConfig = publicConfig || {searchConfig: {initialSearch: true}};
        options = options || {};

        fixture.innerHTML = componentNames.map(function (component) {
            var configId = component + '-config';

            return '<div data-vue-hawksearch-component="' + component +
                '" data-vue-hawksearch-config="' + configId + '"></div>' +
                '<script type="application/json" id="' + configId + '">' +
                JSON.stringify(publicConfig) + '</script>';
        }).join('');
        document.body.appendChild(fixture);
        fixtures.push(fixture);

        getJSONSpy.and.returnValue(request);

        window.BASE_URL = 'https://store.example/';
        window.hawksearchConfig = {vueComponent: 'results'};
        if (options.missingConfig) {
            delete window.hawksearchConfig;
        }

        window.HawksearchVue = {
            createWidget: function (element, widgetOptions) {
                if (element.dataset.vueHawksearchComponent === options.failComponent) {
                    throw new Error('widget creation failed');
                }

                var widget = {config: widgetOptions.config, element: element};

                created.push({element: element, options: widgetOptions, widget: widget});
                return widget;
            },
            getWidgetStore: function (widget) {
                if (options.storeError) {
                    throw options.storeError;
                }

                return {
                    subscribeAction: function (handlers) {
                        subscriptions.push({widget: widget, handlers: handlers});
                    }
                };
            }
        };

        window.requirejs.undef(moduleName);
        load = new Promise(function (resolve, reject) {
            require([moduleName], function () {
                window.setTimeout(resolve, 0);
            }, reject);
        });

        return {
            load: load,
            request: request,
            resolve: function (sections) {
                request.resolve(sections);
            },
            reject: function (error) {
                request.reject(error);
            }
        };
    }

    describe('HawkSearch_EsIndexing/js/common', function () {
        beforeEach(function () {
            previousGlobals = {
                BASE_URL: window.BASE_URL,
                hawksearchConfig: window.hawksearchConfig,
                HawksearchVue: window.HawksearchVue,
                hawksearch: window.hawksearch
            };
            created = [];
            subscriptions = [];
            eventCalls = [];
            errors = [];

            getJSONSpy = spyOn($, 'getJSON');
            spyOn(urlBuilder, 'setBaseUrl');
            spyOn(urlBuilder, 'build').and.returnValue('https://store.example/customer/section/load');
            spyOn(Events, 'trigger').and.callFake(function (name, args) {
                eventCalls.push({name: name, args: args});
            });
            spyOn(Events, 'on');
            spyOn(console, 'error').and.callFake(function () {
                errors.push(Array.prototype.slice.call(arguments));
            });
            spyOn(console, 'warn');
        });

        afterEach(function () {
            fixtures.forEach(function (fixture) {
                fixture.remove();
            });
            fixtures = [];

            ['BASE_URL', 'hawksearchConfig', 'HawksearchVue', 'hawksearch'].forEach(function (name) {
                if (previousGlobals[name] === undefined) {
                    delete window[name];
                } else {
                    window[name] = previousGlobals[name];
                }
            });
        });

        it('loads private config before creating and registering each widget', function () {
            var state = setup();

            return state.load.then(function () {
                expect(urlBuilder.setBaseUrl).toHaveBeenCalledWith('https://store.example/');
                expect(urlBuilder.build).toHaveBeenCalledWith('customer/section/load');
                expect(getJSONSpy).toHaveBeenCalledWith(
                    'https://store.example/customer/section/load',
                    {sections: 'hawksearch-private-vue-config'}
                );
                expect(created.length).toBe(0);

                state.resolve({'hawksearch-private-vue-config': {
                    config: {clientRemoteAddress: '198.51.100.42'},
                    data_id: 123
                }});

                expect(created.length).toBe(3);
                created.forEach(function (item, index) {
                    expect(item.options.config.clientRemoteAddress).toBe('198.51.100.42');
                    expect(item.options.config.data_id).toBeUndefined();
                    expect(eventCalls[index * 2].name).toBe('createWidget:before');
                    expect(eventCalls[index * 2].args.config).toBe(item.options.config);
                    expect(eventCalls[index * 2 + 1].name).toBe('createWidget:after');
                    expect(eventCalls[index * 2 + 1].args.vueWidget).toBe(item.widget);
                });
                expect(window.hawksearch.getVueWidget('results')).toBe(created[1].widget);
                expect(subscriptions.length).toBe(1);
                expect(subscriptions[0].widget).toBe(created[1].widget);
                subscriptions[0].handlers.after({type: 'example', payload: 42}, {});
                expect(eventCalls[eventCalls.length - 1].name).toBe('action:example:after');
                expect(eventCalls[eventCalls.length - 1].args.vueWidget).toBe(created[1].widget);
            });
        });

        it('creates widgets when the private resolver returns a null address', function () {
            var state = setup();

            return state.load.then(function () {
                state.resolve({'hawksearch-private-vue-config': {config: {clientRemoteAddress: null}}});

                expect(created.length).toBe(3);
                expect(created[0].options.config.clientRemoteAddress).toBeNull();
                expect(errors.length).toBe(0);
            });
        });

        it('merges private properties with precedence and independent widget configs', function () {
            var state = setup(undefined, {
                clientGuid: 'public-guid',
                retained: true,
                nested: {publicOnly: true}
            });

            return state.load.then(function () {
                state.resolve({'hawksearch-private-vue-config': {
                    config: {
                        clientGuid: 'private-guid',
                        customSetting: false,
                        nested: {privateOnly: true}
                    },
                    data_id: 123
                }});

                created.forEach(function (item) {
                    expect(item.options.config.clientGuid).toBe('private-guid');
                    expect(item.options.config.customSetting).toBe(false);
                    expect(item.options.config.retained).toBe(true);
                    expect(item.options.config.nested.publicOnly).toBeUndefined();
                    expect(item.options.config.nested.privateOnly).toBe(true);
                    expect(item.options.config.data_id).toBeUndefined();
                });
                created[0].options.config.nested.privateOnly = false;
                expect(created[1].options.config.nested.privateOnly).toBe(true);
            });
        });

        it('logs a failed section request and initializes widgets without private config', function () {
            var state = setup();
            var error = {status: 500};

            return state.load.then(function () {
                state.reject(error);

                expect(created.length).toBe(3);
                expect(created[0].options.config.clientRemoteAddress).toBeUndefined();
                expect(subscriptions.length).toBe(1);
                expect(errors.length).toBe(1);
                expect(errors[0][1]).toBe(error);
            });
        });

        it('logs malformed section data and initializes widgets', function () {
            var responses = [
                {},
                {'hawksearch-private-vue-config': {}},
                {'hawksearch-private-vue-config': {config: []}},
                {'hawksearch-private-vue-config': {config: 'invalid'}}
            ];

            return responses.reduce(function (chain, response) {
                return chain.then(function () {
                    var state = setup();

                    return state.load.then(function () {
                        state.resolve(response);
                        expect(errors.length).toBeGreaterThan(0);
                        expect(created.length).toBe(3);
                        expect(subscriptions.length).toBe(1);
                    });
                });
            }, Promise.resolve());
        });

        it('does not request private config on pages without widgets', function () {
            var state = setup([]);

            return state.load.then(function () {
                expect(getJSONSpy).not.toHaveBeenCalled();
                expect(created.length).toBe(0);

                var ready = false;

                window.hawksearch.whenVueWidgetsReady().done(function () {
                    ready = true;
                });
                expect(ready).toBe(true);
            });
        });

        it('notifies early and late subscribers only after widgets and actions are ready', function () {
            var state = setup();
            var notifications = [];

            return state.load.then(function () {
                window.hawksearch.whenVueWidgetsReady().done(function () {
                    notifications.push([created.length, subscriptions.length]);
                });
                expect(notifications.length).toBe(0);

                state.resolve({'hawksearch-private-vue-config': {config: {}}});
                window.hawksearch.whenVueWidgetsReady().done(function () {
                    notifications.push([created.length, subscriptions.length]);
                });
                expect(notifications).toEqual([[3, 1], [3, 1]]);
            });
        });

        it('resolves readiness after a failed private request and fallback initialization', function () {
            var state = setup();
            var ready = false;

            return state.load.then(function () {
                window.hawksearch.whenVueWidgetsReady().done(function () {
                    ready = true;
                });
                state.reject({status: 500});

                expect(ready).toBe(true);
                expect(created.length).toBe(3);
            });
        });

        it('rejects readiness when configuration or action subscription fails', function () {
            var missing = setup(undefined, undefined, {missingConfig: true});

            return missing.load.then(function () {
                var missingError;

                window.hawksearch.whenVueWidgetsReady().fail(function (error) {
                    missingError = error;
                });
                expect(missingError.message).toMatch(/configuration is not defined/);

                var storeError = new Error('store unavailable');
                var state = setup(undefined, undefined, {storeError: storeError});

                return state.load.then(function () {
                    var subscriptionError;

                    window.hawksearch.whenVueWidgetsReady().fail(function (error) {
                        subscriptionError = error;
                    });
                    state.resolve({'hawksearch-private-vue-config': {config: {}}});
                    expect(subscriptionError).toBe(storeError);
                });
            });
        });

        it('resolves readiness after logging an individual widget creation failure', function () {
            var state = setup(undefined, undefined, {failComponent: 'searchbox'});
            var ready = false;

            return state.load.then(function () {
                window.hawksearch.whenVueWidgetsReady().done(function () {
                    ready = true;
                });
                state.resolve({'hawksearch-private-vue-config': {config: {}}});

                expect(ready).toBe(true);
                expect(errors.length).toBe(1);
                expect(created.length).toBe(2);
                expect(Object.keys(window.hawksearch.getVueWidget('searchbox')).length).toBe(0);
                expect(window.hawksearch.getVueWidget('results')).toBe(created[0].widget);
            });
        });
    });
});
