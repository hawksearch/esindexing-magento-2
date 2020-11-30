<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

class Indexing extends ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    public const CONFIG_ITEMS_BATCH_SIZE = 'items_batch_size';
    /**#@-*/

    /**
     * Return items batch size for indexing job single operation
     * @param null|int|string $store
     * @return int
     */
    public function getItemsBatchSize($store = null): int
    {
        return (int)$this->getConfig(self::CONFIG_ITEMS_BATCH_SIZE, $store);
    }

}
