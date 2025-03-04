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

use HawkSearch\EsIndexing\Model\Indexer\Category as CategoryIndexer;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;

class ProductPlugin extends AbstractPlugin
{
    private IndexerInterface $categoryIndexer;
    private ProductDataProvider $productDataProvider;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        ProductDataProvider $productDataProvider
    ) {
        parent::__construct($indexerRegistry);
        $this->categoryIndexer = $indexerRegistry->get(CategoryIndexer::INDEXER_ID);
        $this->productDataProvider = $productDataProvider;
    }

    /**
     * Reindex on product save.
     *
     * @throws \Exception
     */
    public function aroundSave(
        ProductResource $productResource,
        \Closure $proceed,
        AbstractModel $object
    ): ProductResource {
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex on product delete
     *
     * @throws \Exception
     */
    public function aroundDelete(
        ProductResource $productResource,
        \Closure $proceed,
        AbstractModel $object
    ): ProductResource {
        $object->setAffectedCategoryIds($object->getCategoryIds());
        $object->setAffectedParentProductIds($this->productDataProvider->getParentProductIds([$object->getId()]));
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @throws \Exception
     */
    private function addCommitCallback(
        ProductResource $productResource,
        \Closure $proceed,
        AbstractModel $object
    ): ProductResource {
        try {
            $productResource->getConnection()->beginTransaction();
            $result = $proceed($object);
            $productResource->addCommitCallback(function () use ($object) {
                $affectedCategories = $object->getAffectedCategoryIds();
                if (is_array($affectedCategories)) {
                    $this->reindexCategoryList($affectedCategories);
                }
                $affectedProductIds = $object->getAffectedParentProductIds() ?? [];
                $ids = array_merge([$object->getId()], $affectedProductIds);
                $this->reindexList($ids);
            });
            $productResource->getConnection()->commit();
        } catch (\Exception $e) {
            $productResource->getConnection()->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Reindex categories if indexer is not scheduled
     *
     * @param int[] $categoryIds
     */
    private function reindexCategoryList(array $categoryIds): void
    {
        if (!$this->categoryIndexer->isScheduled()) {
            $this->categoryIndexer->reindexList($categoryIds);
        }
    }
}
