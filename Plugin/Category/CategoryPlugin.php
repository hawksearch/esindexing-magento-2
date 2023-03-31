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
    /**
     * @var IndexerInterface
     */
    private $categoryIndexer;

    /**
     * @var IndexerInterface
     */
    private $productIndexer;

    /**
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->categoryIndexer = $indexerRegistry->get(CategoryIndexer::INDEXER_ID);
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
    }

    /**
     * Reindex on page save.
     *
     * @param CategoryResource $categoryResource
     * @param \Closure $proceed
     * @param CategoryModel $object
     * @return CategoryResource
     * @throws \Exception
     */
    public function aroundSave(CategoryResource $categoryResource, \Closure $proceed, CategoryModel $object)
    {
        return $this->addCommitCallback($categoryResource, $proceed, $object);
    }

    /**
     * @param CategoryResource $categoryResource
     * @param \Closure $proceed
     * @param CategoryModel $object
     * @return CategoryResource
     * @throws \Exception
     */
    public function aroundDelete(CategoryResource $categoryResource, \Closure $proceed, CategoryModel $object)
    {
        $affectedProductsOrig = $object->getAffectedProductIds();
        $object->setAffectedProductIds(array_keys($object->getProductsPosition()));
        $result = $this->addCommitCallback($categoryResource, $proceed, $object);
        $object->setAffectedProductIds($affectedProductsOrig);

        return $result;
    }

    /**
     * Reindex catalog search.
     *
     * @param CategoryResource $categoryResource
     * @param \Closure $proceed
     * @param CategoryModel $category
     * @return CategoryResource
     * @throws \Exception
     */
    private function addCommitCallback(CategoryResource $categoryResource, \Closure $proceed, CategoryModel $category)
    {
        try {
            $categoryResource->beginTransaction();
            $result = $proceed($category);
            $categoryResource->addCommitCallback(function () use ($category) {
                $affectedProducts = $category->getAffectedProductIds();
                if (is_array($affectedProducts)) {
                    $this->reindexProductList($affectedProducts);
                }
                $this->reindexCategoryRow($category->getId());
            });
            $categoryResource->commit();
        } catch (\Exception $e) {
            $categoryResource->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Reindex category if indexer is not scheduled
     *
     * @param int $pageId
     * @return void
     */
    protected function reindexCategoryRow($pageId)
    {
        if (!$this->categoryIndexer->isScheduled()) {
            $this->categoryIndexer->reindexRow($pageId);
        }
    }

    /**
     * Reindex products if indexer is not scheduled
     *
     * @param int[] $productIds
     * @return void
     */
    protected function reindexProductList(array $productIds)
    {
        if (!$this->productIndexer->isScheduled()) {
            $this->productIndexer->reindexList($productIds);
        }
    }

}
