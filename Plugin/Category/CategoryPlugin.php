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

namespace HawkSearch\EsIndexing\Plugin\Category;

use HawkSearch\EsIndexing\Model\Indexer\Category as CategoryIndexer;
use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;

class CategoryPlugin
{
    private IndexerInterface $categoryIndexer;
    private IndexerInterface $productIndexer;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->categoryIndexer = $indexerRegistry->get(CategoryIndexer::INDEXER_ID);
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
    }

    /**
     * Reindex on page save.
     *
     * @throws \Exception
     */
    public function aroundSave(
        CategoryResource $categoryResource,
        \Closure $proceed,
        CategoryModel $object
    ): CategoryResource {
        return $this->addCommitCallback($categoryResource, $proceed, $object);
    }

    /**
     * @throws \Exception
     */
    public function aroundDelete(
        CategoryResource $categoryResource,
        \Closure $proceed,
        CategoryModel $object
    ): CategoryResource {
        $object->setAffectedProductIds(array_keys($object->getProductsPosition()));
        return $this->addCommitCallback($categoryResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @throws \Exception
     */
    private function addCommitCallback(
        CategoryResource $categoryResource,
        \Closure $proceed,
        CategoryModel $category
    ): CategoryResource {
        try {
            $categoryResource->getConnection()->beginTransaction();
            $result = $proceed($category);
            $categoryResource->addCommitCallback(function () use ($category) {
                $affectedProducts = $category->getAffectedProductIds();
                if (is_array($affectedProducts)) {
                    $this->reindexProductList($affectedProducts);
                }
                $this->reindexCategoryRow((int)$category->getId());
            });
            $categoryResource->getConnection()->commit();
        } catch (\Exception $e) {
            $categoryResource->getConnection()->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Reindex category if indexer is not scheduled
     */
    private function reindexCategoryRow(int $pageId): void
    {
        if (!$this->categoryIndexer->isScheduled()) {
            $this->categoryIndexer->reindexRow($pageId);
        }
    }

    /**
     * Reindex products if indexer is not scheduled
     *
     * @param int[] $productIds
     */
    private function reindexProductList(array $productIds): void
    {
        if (!$this->productIndexer->isScheduled()) {
            $this->productIndexer->reindexList($productIds);
        }
    }

}
