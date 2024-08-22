/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
    'mage/utils/template',
    'priceUtils',
    'hawksearchVueEvents',
    'hawksearchVueSDK',
    'mage/adminhtml/tools'
], function ($, _, mageTemplate, priceUtils, Events) {
    window.hawksearch = {
        /**
         * Find Vue app widget in registered widget instances
         *
         * @param component
         * @returns {*|{}}
         */
        getVueWidget: function(component) {
            let searchCriteria = function (item) {
                let byName = component === undefined || item.$el.dataset.vueHawksearchComponent === component
                return !_.isUndefined(item._isVue)
                    && item._isVue
                    && byName;
            }
            return _.find(HawksearchVue.widgetInstances, searchCriteria) || {};
        },

        /**
         * Return encoded value of current URL with query parameters
         *
         * @returns {string}
         */
        getRedirectUrlEncoded: function() {
            const url = hawksearchConfig.request.url + window.location.search;
            return window.Base64.encode(url);
        },

        /**
         * Return valid form_key
         *
         * @returns {string}
         */
        getFormKey: function () {
            const updatedFormKey = $('input[name="form_key"]').val();

            if (updatedFormKey && updatedFormKey !== hawksearchConfig.request.formKey) {
                return updatedFormKey;
            }

            return hawksearchConfig.request.formKey;
        },

        /**
         *
         * @param document Result Document
         * @returns {string|null}
         */
        extractId: function(document) {
            const uuid = this.getDocumentField(document, '__uid');
            if (uuid && uuid.includes('_')) {
                return uuid.split('_').pop();
            }

            return null;
        },

        /**
         * Apply pricing template
         *
         * @param document results Document
         * @returns {*}
         */
        formatItemPrice: function (document) {
            const templateType = (
                _.indexOf(
                    _.keys(hawksearchConfig.pricing.priceTemplates),
                    this.getDocumentField(document, "type_id")
                ) !== -1
            ) ?
                this.getDocumentField(document, "type_id") :
                'default';
            /**
             * We use regular price template for now
             * @todo select price template regular or special depending on price
             */
            const priceTemplate = hawksearchConfig.pricing.priceTemplates[templateType]['regular'];

            return mageTemplate.template(
                priceTemplate,
                {
                    uid: this.extractId(document),
                    price_regular: this.getDocumentField(document, "price_regular"),
                    price_regular_include_tax: this.getDocumentField(document, "price_regular_include_tax"),
                    price_final: this.getDocumentField(document, "price_final"),
                    price_final_include_tax: this.getDocumentField(document, "price_final_include_tax"),

                    //priceUtils.formatPrice(item.price, currencyFormat)
                    price_regular_formatted: priceUtils.formatPriceLocale(
                        this.getDocumentField(document, "price_regular"),
                        hawksearchConfig.pricing.priceFormat
                    ),
                    price_regular_include_tax_formatted: priceUtils.formatPriceLocale(
                        this.getDocumentField(document, "price_regular_include_tax"),
                        hawksearchConfig.pricing.priceFormat
                    ),
                    price_final_formatted: priceUtils.formatPriceLocale(
                        this.getDocumentField(document, "price_final"),
                        hawksearchConfig.pricing.priceFormat
                    ),
                    price_final_include_tax_formatted: priceUtils.formatPriceLocale(
                        this.getDocumentField(document, "price_final_include_tax"),
                        hawksearchConfig.pricing.priceFormat
                    ),
                    //currency_code: hawksearchConfig.pricing.currencyCode
                }
            )
        },

        formatItemUrl: function (document) {
            const replaceProductUrl = hawksearchConfig.catalog?.isCategoryPage
                && hawksearchConfig.catalog?.useCategoryPathInProductUrl
                && this.getDocumentField(document, '__type') === 'product',
                id = this.extractId(document);

            if (!replaceProductUrl || !id) {
                return;
            }

            this.initItemUrlSlug(document);

            let productUrl = this.getDocumentField(document, 'url');
            if (hawksearchConfig.catalog.categoryProducts?.productUrlRewriteExceptions[id] !== undefined) {
                //use custom url rewrite
                productUrl = hawksearchConfig.catalog.categoryProducts.productUrlRewriteExceptions[id];
            } else if (!(hawksearchConfig.catalog.categoryProducts?.noUrlRewriteProducts.indexOf(parseInt(id)) !== -1 ||
                hawksearchConfig.catalog.categoryProducts?.noUrlRewriteProducts.indexOf(String(id)) !== -1)) {
                //use URL template
                productUrl = mageTemplate.template(
                    hawksearchConfig.catalog.productUrlTemplate,
                    {
                        product_url_slug: this.getDocumentField(document, 'url_slug')
                    }
                );
            }
            this.setDocumentField(document, 'url', productUrl);
        },

        initItemUrlSlug: function(document) {
            if (!this.getDocumentField(document, 'url_slug')) {
                this.setDocumentField(
                    document,
                    'url_slug',
                    this.getDocumentField(document, 'url')
                );
            }
        },

        updateSearchOutput: function (searchOutput) {
            if (!searchOutput?.Results?.length) {
                return;
            }
            var results = searchOutput.Results;

            results.forEach(function updateResultItem(item) {
                this.formatItemUrl(item.Document);
            }, this);
        },

        /**
         * Return field value from the Document
         *
         * @param document results Document
         * @param {string} field
         * @returns {*|null}
         */
        getDocumentField: function (document, field) {
            if (document &&
                document[field] &&
                document[field].length) {
                return document[field][0];
            }

            return null;
        },

        /**
         * Set field value in the Document
         *
         * @param document results Document
         * @param {string} field
         * @param {*} value
         */
        setDocumentField: function (document, field, value) {
            if (document) {
                if (document[field] === undefined) {
                    document[field] = [];
                }
                document[field][0] = value;
            }
        }
    }

    function initVueWidget() {
        const components = $('[data-vue-hawksearch-component]');

        $.each(components, function (index, component) {
            try {
                const configId = $(component).data('vueHawksearchConfig');
                const config = JSON.parse($('#' + configId).html());

                Events.trigger('createWidget:before', {
                    component: component,
                    config: config
                });
                widget = HawksearchVue.createWidget(component, {config, dataLayer: configId});
                Events.trigger('createWidget:after', {
                    vueWidget: widget
                });
            } catch (e) {
                console.error(e);
            }
        });
    }

    let isFetchResultsDispatched = false;

    /**
     * Attach event handlers to HawkSearch Vue SDK events.
     *
     * To subscribe to Vuex store actions use the following format of an event name:
     *  action:<vuex-store-action>:<action-position>
     *      <vuex-store-action> is any Vuex action name
     *      <action-position> could be 'before' or 'after'
     *  Example of an action: action:fetchResults:after
     *
     * To subscribe to custom Vue events triggered by $emit() method put all of your
     * custom event handlers inside the event 'createWidget:after', i.e.
     * Events.on('createWidget:after', function(args) {
     *      args.vueWidget.$on('<vueCustomEventName>', () => {
     *          // custom event handler logic
     *      }
     * }
     *
     */
    function bindEvents() {
        Events.on('action:fetchResults:after', function(args) {
            $(args.vueWidget.$el).trigger('contentUpdated');
            isFetchResultsDispatched = true;


        });

        Events.on('createWidget:after', function(args) {
            args.vueWidget.$on('urlUpdated', () => {
                if (!isFetchResultsDispatched) {
                    return;
                }

                const cartForms = $('[data-role="tocart-form"]');
                $.each(cartForms, (key, form) => {
                    form.elements[hawksearchConfig.request.urlEncodedParam].value = hawksearch.getRedirectUrlEncoded();
                });
                isFetchResultsDispatched = false;
            });

            args.vueWidget.$on('resultsupdate', (searchOutput) => {
                if (searchOutput) {
                    hawksearch.updateSearchOutput(searchOutput);
                }
            });
        });
    }

    $(function ($) {
        if (typeof hawksearchConfig === 'undefined') {
            console.warn("'hawksearchConfig' is not defined. HawkSearch Vue SDK initialization is terminated.");
            return;
        }

        bindEvents();
        initVueWidget();

        let vueWidget = hawksearch.getVueWidget(hawksearchConfig.vueComponent);
        if (!_.isEmpty(vueWidget) && vueWidget.config.searchConfig.initialSearch) {

            /**
             *
             * @param {String} actionHandler defines whether the subscribe handler is called before or after an action dispatch
             * @param {Object} action Store action descriptor
             * @param {Object} state Store state
             */
            let triggerEvent = (actionHandler, action, state) => {
                let eventName = 'action' + ':' + action.type + ':' + actionHandler;
                Events.trigger(eventName, {
                    payload: action.payload,
                    state: state,
                    vueWidget: hawksearch.getVueWidget(hawksearchConfig.vueComponent)
                });
            };

            HawksearchVue.getWidgetStore(vueWidget).subscribeAction({
                before: (action, state) => {
                    triggerEvent('before', action, state);
                },
                after: (action, state) => {
                    triggerEvent('after', action, state);
                }
            });
        }
    });
});
