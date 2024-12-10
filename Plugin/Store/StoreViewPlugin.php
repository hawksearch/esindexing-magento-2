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

use HawkSearch\EsIndexing\Model\Indexer\Category as CategoryIndexer;
use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;
use Magento\Store\Model\Store as StoreModel;

class StoreViewPlugin extends AbstractPlugin
{
    /**
     * Invalidate indexer on store view save
     *
     * @return StoreResourceModel
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(StoreResourceModel $subject, StoreResourceModel $result, AbstractModel $store)
    {
        if ($this->validate($store)) {
            $this->indexerRegistry->get(CategoryIndexer::INDEXER_ID)->invalidate();
            $this->indexerRegistry->get(ProductIndexer::INDEXER_ID)->invalidate();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function validate(AbstractModel $model)
    {
        /** @var StoreModel $model */
        return !$model->isObjectNew()
            && $model->dataHasChangedFor('group_id')
            && $this->indexingConfig->isIndexingEnabled($model->getId());
    }
}
