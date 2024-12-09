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
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;

class ProductPlugin extends AbstractPlugin
{
    /**
     * @var IndexerInterface
     */
    private $categoryIndexer;

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

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
     * @return ProductResource
     * @throws \Exception
     */
    public function aroundSave(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex on product delete
     *
     * @return ProductResource
     * @throws \Exception
     */
    public function aroundDelete(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        $object->setAffectedCategoryIds($object->getCategoryIds());
        $object->setAffectedParentProductIds($this->productDataProvider->getParentProductIds([$object->getId()]));
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @return ProductResource
     * @throws \Exception
     */
    private function addCommitCallback(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        try {
            $productResource->beginTransaction();
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
            $productResource->commit();
        } catch (\Exception $e) {
            $productResource->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Reindex categories if indexer is not scheduled
     *
     * @param int[] $categoryIds
     * @return void
     */
    protected function reindexCategoryList(array $categoryIds)
    {
        if (!$this->categoryIndexer->isScheduled()) {
            $this->categoryIndexer->reindexList($categoryIds);
        }
    }
}
