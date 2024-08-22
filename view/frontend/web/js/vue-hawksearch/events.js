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

define(['uiEvents'], function (uiEvents) {
    'use strict';

    return {
        ns: 'hawksearchVue:',
        /**
         * Calls callback when name event is triggered
         *
         * @param {String} events
         * @param {Function} callback
         * @param {Function} ns
         * @return {Object}
         */
        on: function (events, callback, ns) {
            uiEvents.on(this.ns + events, callback, this.ns + ns);

            return this;
        },

        /**
         * Removed callback from listening to target event
         *
         * @param {String} ns
         * @return {Object}
         */
        off: function (ns) {
            uiEvents.off(this.ns + ns);

            return this;
        },

        /**
         * Triggers event and executes all attached callbacks
         *
         * @param {String} name
         * @param {any} args
         * @returns {Boolean}
         */
        trigger: function (name, args) {
            return uiEvents.trigger(this.ns + name, args);
        }
    };
});
