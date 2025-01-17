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

namespace HawkSearch\EsIndexing\Model\Indexing;

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;

/**
 * @api
 * @since 0.8.0
 */
abstract class AbstractConfigHelper
{
    private IndexingConfig $indexingConfig;

    public function __construct(
        IndexingConfig $indexingConfig
    )
    {
        $this->indexingConfig = $indexingConfig;
    }

    /**
     * @param null|int|string $store
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    public function isEnabled($store = null)
    {
        return $this->indexingConfig->isIndexingEnabled($store);
    }

    /**
     * @param null|int|string $store
     * @return int
     * @noinspection PhpMissingParamTypeInspection
     */
    public function getBatchSize($store = null)
    {
        return $this->indexingConfig->getItemsBatchSize($store);
    }
}
