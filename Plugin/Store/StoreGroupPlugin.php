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
use HawkSearch\EsIndexing\Model\Indexer\Category as CategoryIndexer;
use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Group as StoreGroupModel;
use Magento\Store\Model\ResourceModel\Group as StoreGroupResourceModel;
use Magento\Store\Model\Store;

class StoreGroupPlugin extends AbstractPlugin
{
    private IndexerInterface $productIndexer;
    private IndexerInterface $categoryIndexer;
    private IndexingConfig $indexingConfig;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        IndexingConfig $indexingConfig
    )
    {
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
        $this->categoryIndexer = $indexerRegistry->get(CategoryIndexer::INDEXER_ID);
        $this->indexingConfig = $indexingConfig;
    }

    /**
     * Invalidate indexer on store group save
     *
     * @param StoreGroupResourceModel $subject
     * @param StoreGroupResourceModel $result
     * @param StoreGroupModel $group
     * @return StoreGroupResourceModel
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(StoreGroupResourceModel $subject, StoreGroupResourceModel $result, AbstractModel $group)
    {
        if ($this->validate($group)) {
            $this->categoryIndexer->invalidate();
            $this->productIndexer->invalidate();
        }

        return $result;
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @param StoreGroupModel $model
     * @return bool
     */
    protected function validate(AbstractModel $model)
    {
        $isIndexingEnabled = false;
        /** @var Store $store */
        foreach ($model->getStores() as $store) {
            $isIndexingEnabled = $isIndexingEnabled || $this->indexingConfig->isIndexingEnabled($store->getId());
        }

        return ($model->dataHasChangedFor('website_id') || $model->dataHasChangedFor('root_category_id'))
            && !$model->isObjectNew() && $isIndexingEnabled;
    }
}
