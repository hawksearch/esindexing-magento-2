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
   'uiClass',
   'mage/translate',
   'prototype'
], function ($, UiClass) {
    'use strict';

    var ArrayRowExtended = {
        defaults: {
            baseClassPrefix: 'arrayRow',
            addButtonPrefix: 'addToEndBtn',
            tbodyIdPrefix: 'addRow',
            addedRowCssClass: 'row-added',
            newFieldWrapperCssClass: 'new-field-wrapper',
            newFieldInputNameSuffix: '[field_new]',
            htmlId: '',
            newFieldOptionValue: '--insert--new--',
            fieldSelectSelector: 'select[name$=\"[field]\"]',
            fieldSelectNameSuffix: '[field]',
        },

        /**
         * Initialize actions and adapter.
         *
         * @param {Object} config
         * @returns {Object}
         */
        initialize: function (config) {
            return this._super()
                .initEvents();
        },

        initConfig: function() {
            this._super();
            this.tbodySelector = '#' + this.tbodyIdPrefix + this.htmlId;

            return this;
        },

        add: function (rowData, insertAfterId) {
            this._super();

            var newFieldOptionText = $.mage.__('--Create New Field--'),
                newFieldInputPlaceholder = $.mage.__('Enter Field Name');

            var lastAddedRow = $(this.tbodySelector).children().last();
            lastAddedRow.addClass(this.addedRowCssClass);
            var fieldSelect = lastAddedRow.find(this.fieldSelectSelector).first();
            if (fieldSelect.length) {
                var firstOption = fieldSelect.find('option').first();
                if (firstOption.val() === '') {
                    // Add new option in Field select element
                    firstOption.after(
                        $('<option/>')
                            .text(newFieldOptionText)
                            .val(this.newFieldOptionValue)
                    );

                    // Add New Field Name input text element
                    var newFieldWrapper = $('<div></div>').attr({
                        'class': this.newFieldWrapperCssClass
                    }).append(
                        $('<input>').attr({
                            'name': fieldSelect.attr('name')
                                .replace(this.fieldSelectNameSuffix, this.newFieldInputNameSuffix),
                            'type': 'text',
                            'placeholder': newFieldInputPlaceholder
                        })
                    ).hide();
                    fieldSelect.after(newFieldWrapper)
                        .on('change', this.switchNewField.bind(this));
                }
            }
        },

        initEvents: function () {
            var addButtonId = this.addButtonPrefix + this.htmlId;

            Event.stopObserving(addButtonId, 'click');
            Event.observe(addButtonId,
                'click',
                this.add.bind(
                    this, false, false
                )
            );
        },

        switchNewField: function (event) {
            var selectedOption = $(event.target).find('option:selected').first();
            if (selectedOption.val() === this.newFieldOptionValue) {
                $(event.target).parent()
                    .find('.' + this.newFieldWrapperCssClass)
                    .show()
            } else {
                $(event.target).parent()
                    .find('.' + this.newFieldWrapperCssClass)
                    .hide()
            }
        }
    };

    return function (moduleConfig) {
        if (!moduleConfig.htmlId) {
            return;
        }

        var addRowBaseClassName = moduleConfig.baseClassPrefix + moduleConfig.htmlId;

        var addRowBaseUiClass = UiClass.extend(window[addRowBaseClassName] || {}).extend(ArrayRowExtended);

        return new addRowBaseUiClass(moduleConfig);
    }
});
