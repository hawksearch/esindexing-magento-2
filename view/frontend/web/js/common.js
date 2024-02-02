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
    'hawksearchVueSDK',
    'mage/adminhtml/tools'
], function ($, _, mageTemplate, priceUtils) {
    window.hawksearch = {
        /**
         * Find Vue app widget in registered widget instances
         *
         * @param component
         * @returns {*|{}}
         */
        getVueWidget: function(component) {
            let searchCriteria = function (item) {
                return !_.isUndefined(item._isVue)
                    && item._isVue
                    && item.$el.dataset.vueHawksearchComponent === component;
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
        }
    }

    function initVueWidget() {
        const components = $('[data-vue-hawksearch-component]');

        $.each(components, function (index, component) {
            try {
                const configId = $(component).data('vueHawksearchConfig');
                const config = JSON.parse($('#' + configId).html());
                HawksearchVue.createWidget(component, {config, dataLayer: configId});
            } catch (e) {
                console.error(e);
            }
        });
    }

    $(function ($) {
        initVueWidget();

        let isFetchResultsDispatched = false;
        if (hawksearch.getVueWidget(hawksearchConfig.vueComponent).config.searchConfig.initialSearch) {
            HawksearchVue.getWidgetStore(hawksearch.getVueWidget(hawksearchConfig.vueComponent)).subscribeAction({
                after: (action, state) => {
                    if (action.type !== 'fetchResults') {
                        return;
                    }
                    isFetchResultsDispatched = true;
                }
            });
            hawksearch.getVueWidget(hawksearchConfig.vueComponent).$on('urlUpdated', () => {
                if (!isFetchResultsDispatched) {
                    return;
                }

                const cartForms = $('[data-role="tocart-form"]');
                $.each(cartForms, (key, form) => {
                    form.elements[hawksearchConfig.request.urlEncodedParam].value = hawksearch.getRedirectUrlEncoded();
                });
                isFetchResultsDispatched = false;
            });
        }
    });
});
