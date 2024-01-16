<?php
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

declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Config;

use HawkSearch\Connector\Model\ConfigProvider;

class EventTracking extends ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    public const CONFIG_ENABLE = 'enable';
    /**#@-*/

    public const COOKIE_ADD_TO_CART_NAME = 'hawksearch_event_tracking_add_to_cart';
    public const COOKIE_REMOVE_FROM_CART_NAME = 'hawksearch_event_tracking_remove_from_cart';

    /**
     * Check if Event Tracking is enabled for selected store
     * @param null|int|string $store
     * @return bool
     */
    public function isEnabled($store = null): bool
    {
        return !!$this->getConfig(self::CONFIG_ENABLE, $store);
    }
}
