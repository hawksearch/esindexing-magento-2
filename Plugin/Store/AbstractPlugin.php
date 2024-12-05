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

namespace HawkSearch\EsIndexing\Plugin\Store;

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;

abstract class AbstractPlugin
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var IndexingConfig
     */
    protected $indexingConfig;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        IndexingConfig $indexingConfig
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->indexingConfig = $indexingConfig;
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @return bool
     */
    abstract protected function validate(AbstractModel $model);
}
