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

namespace HawkSearch\EsIndexing\Plugin\Product;

use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;

abstract class AbstractPlugin
{
    /**
     * @var IndexerInterface
     */
    protected $productIndexer;

    /**
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
    }

    /**
     * Reindex product if indexer is not scheduled
     *
     * @param int $productId
     * @return void
     */
    protected function reindexRow(int $productId)
    {
        if (!$this->productIndexer->isScheduled()) {
            $this->productIndexer->reindexRow($productId);
        }
    }

    /**
     * Reindex product if indexer is not scheduled
     *
     * @param int[] $productIds
     * @return void
     */
    protected function reindexList(array $productIds)
    {
        if (!$this->productIndexer->isScheduled()) {
            $this->productIndexer->reindexList($productIds);
        }
    }
}
