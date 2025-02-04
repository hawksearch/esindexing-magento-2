<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Product;

use Exception;
use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\ItemsDataProviderInterface;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use HawkSearch\EsIndexing\Model\Product\Attribute\ExcludeNotVisibleProductsFlagInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;

/**
 * @phpstan-type ItemType ProductModel
 * @implements ItemsDataProviderInterface<ItemType>
 */
class ItemsDataProvider implements ItemsDataProviderInterface
{
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Visibility $visibility;
    private Indexing $indexingConfig;
    private CategoryCollectionFactory $categoryCollectionFactory;
    private ExcludeNotVisibleProductsFlagInterface $excludeNotVisibleProductsFlag;
    private ProductDataProvider $productDataProvider;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Visibility $visibility
     * @param Indexing $indexingConfig
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param ExcludeNotVisibleProductsFlagInterface|null $excludeNotVisibleProductsFlag
     * @param ProductDataProvider|null $productDataProvider
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Visibility $visibility,
        Indexing $indexingConfig,
        CategoryCollectionFactory $categoryCollectionFactory,
        ExcludeNotVisibleProductsFlagInterface $excludeNotVisibleProductsFlag = null,
        ProductDataProvider $productDataProvider = null
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->visibility = $visibility;
        $this->indexingConfig = $indexingConfig;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->excludeNotVisibleProductsFlag = $excludeNotVisibleProductsFlag ?: ObjectManager::getInstance()->get(ExcludeNotVisibleProductsFlagInterface::class);
        $this->productDataProvider = $productDataProvider ?: ObjectManager::getInstance()->get(ProductDataProvider::class);
    }

    /**
     * @throws Exception
     */
    public function getItems(int $storeId, ?array $entityIds = null, int $currentPage = 1, int $pageSize = 0)
    {
        $items = $this->getProductCollection($storeId, $entityIds, $currentPage, $pageSize);

        // Add all parent categories if config flag is set
        if ($this->indexingConfig->isProductsIncludeCategoriesHierarchy()) {
            $this->addParentCategoriesToProducts($items);
        }

        $this->addParentIds($items);
        $this->addChildProducts($items);

        return $items;
    }

    /**
     * @param int $storeId
     * @param array<int>|null $productIds
     * @param int $currentPage
     * @param int $pageSize
     * @return ItemType[]
     */
    protected function getProductCollection(
        int $storeId,
        ?array $productIds = null,
        int $currentPage = 1,
        int $pageSize = 0
    ): array {
        $this->searchCriteriaBuilder->addFilter('store_id', $storeId);

        if ($productIds && count($productIds) > 0) {
            $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in');
        }

        $excludeNotVisibleProducts = $this->excludeNotVisibleProductsFlag->execute();

        if ($excludeNotVisibleProducts) {
            $this->searchCriteriaBuilder->addFilter(
                'visibility',
                $this->visibility->getVisibleInSiteIds(),
                'in'
            );
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setCurrentPage($currentPage)
            ->setPageSize($pageSize);

        return $this->getProductItems($searchCriteria);
    }

    /**
     * @return ItemType[]
     */
    private function getProductItems(SearchCriteria $searchCriteria): array
    {
        // @phpstan-ignore-next-line
        return $this->productRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param ItemType[] $products
     */
    private function addParentCategoriesToProducts(array $products): void
    {
        // Get list of all distinct categories among all products
        $categoryIds = [];
        foreach ($products as $product) {
            foreach ($product->getCategoryIds() as $categoryId) {
                $categoryIds[(int)$categoryId] = true;
            }
        }
        $categoryIds = array_keys($categoryIds);

        if (!$categoryIds) {
            return;
        }

        // Get all categories used in products at once
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', $categoryIds);
        $categories = $collection->getItems();

        // Add parent categories to products
        foreach ($products as $product) {
            $newCategoryIds = $product->getCategoryIds();
            foreach ($product->getCategoryIds() as $categoryId) {
                $category = $categories[(int)$categoryId] ?? null;
                if ($category === null) {
                    continue;
                }

                $parentIds = $category->getParentIds();
                array_push($newCategoryIds, ...$parentIds);
            }
            $newCategoryIds = array_unique($newCategoryIds);
            $product->setCategoryIds($newCategoryIds);
        }
    }

    /**
     * Add parent ids data to loaded items
     *
     * @param ItemType[] $products
     * @throws Exception
     */
    private function addParentIds(array $products): void
    {
        $parentsMap = $this->productDataProvider->getParentsByChildMap(array_keys($products));
        foreach ($products as $item) {
            if (isset($parentsMap[$item->getId()])) {
                $item->setData('parent_ids', $parentsMap[$item->getId()]);
            }
        }
    }

    /**
     * Load child products collection and add its data to loaded product items
     *
     * @param ItemType[] $products
     * @throws Exception
     */
    private function addChildProducts(array $products): void
    {
        if (!$products) {
            return;
        }

        $childrenMap = $this->productDataProvider->getChildrenByParentMap(array_keys($products));
        if (!count($childrenMap)) {
            return;
        }

        $this->searchCriteriaBuilder->addFilter('entity_id', array_merge([], ...$childrenMap), 'in');

        $currentProduct = current($products);
        $storeId = $currentProduct->getStoreId();
        $this->searchCriteriaBuilder->addFilter('store_id', $storeId);

        $children = $this->getProductItems($this->searchCriteriaBuilder->create());

        foreach ($childrenMap as $parentId => $childIds) {
            if (isset($products[$parentId])) {
                $products[$parentId]->setData(
                    'child_products',
                    $products[$parentId]->hasData('child_products')
                        ? $products[$parentId]->getData('child_products')
                        : []
                );

                $childrenSelected = [];
                foreach ($childIds as $childId) {
                    if (!isset($children[$childId])) {
                        continue;
                    }
                    $childrenSelected[$childId] = $children[$childId];
                }
                $products[$parentId]->setData('child_products', $childrenSelected);
            }
        }
    }
}
