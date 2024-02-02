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
    'hawksearchVueSDK',
    'mage/adminhtml/tools'
], function ($, _) {
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
