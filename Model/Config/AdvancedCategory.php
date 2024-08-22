<?php
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

declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Config;

use HawkSearch\Connector\Model\ConfigProvider;

class AdvancedCategory extends ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    public const CONFIG_PRODUCT_URL_REWRITE_EXCEPTIONS_THRESHOLD = 'product_url_rewrite_exceptions_threshold';
    /**#@-*/

    /**
     * Returns a threshold for the amount of excepted Product URL rewrites
     *
     * @param null|int|string $store
     * @return int
     */
    public function getProductUrlRewriteExceptionsThreshold($store = null): int
    {
        return (int)$this->getConfig(self::CONFIG_PRODUCT_URL_REWRITE_EXCEPTIONS_THRESHOLD, $store);
    }

}
