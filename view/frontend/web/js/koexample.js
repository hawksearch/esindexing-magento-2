/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
    'uiComponent'
], function (Component) {
    return Component.extend({
        initialize: function (config) {
            this.config = config;
            this._super();
        },

        getProducts: function () {
            return [
                {
                    'id': 1,
                    'sku': '24-MB01',
                    'url': this.config.urlPart + 'product/' + 1 + '/'
                },
                {
                    'id': 2,
                    'sku': '24-MB04',
                    'url': this.config.urlPart + 'product/' + 2 + '/'
                },
            ];
        }
    });
});
