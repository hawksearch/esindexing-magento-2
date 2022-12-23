<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use HawkSearch\EsIndexing\Model\Config\Indexing;

class ProductItemsProvider implements ItemsProviderInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var Indexing
     */
    private $indexingConfig;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * ProductItemsProvider constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Visibility $visibility
     * @param FilterBuilder $filterBuilder
     * @param Indexing $indexingConfig
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Visibility $visibility,
        FilterBuilder $filterBuilder,
        Indexing $indexingConfig,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->visibility = $visibility;
        $this->filterBuilder = $filterBuilder;
        $this->indexingConfig = $indexingConfig;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @inheritDoc
     * @return ProductInterface[]
     */
    public function getItems($storeId, $entityIds = null, $currentPage = 1, $pageSize = 0)
    {
        return $this->getProductCollection($storeId, $entityIds, $currentPage, $pageSize);
    }

    /**
     * @param int $storeId
     * @param array|null $productIds
     * @param int $currentPage
     * @param int $pageSize
     * @return ProductInterface[]
     */
    protected function getProductCollection($storeId, $productIds = null, $currentPage = 1, $pageSize = 0): array
    {
        $this->searchCriteriaBuilder->addFilter('store_id', $storeId);

        if ($productIds && count($productIds) > 0) {
            $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in');
        }

        //@TODO Define $excludeNotVisibleProducts in system configuration
        $excludeNotVisibleProducts = true;

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

        $items = $this->productRepository->getList($searchCriteria)->getItems();

        // Add all parent categories if config flag is set
        if ($this->indexingConfig->isProductsIncludeCategoriesHierarchy()) {
            $this->addParentCategoriesToProducts($items);
        }

        return $items;
    }

    /**
     * Adds all parent categories to products
     * @param array $products
     * @return void
     */
    private function addParentCategoriesToProducts(array $products): void
    {
        // Get list of all distinct categories among all products
        $categoryIds = [];
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            foreach ($product->getCategoryIds() as $categoryId) {
                $categoryIds[(int)$categoryId] = true;
            }
        }
        $categoryIds = array_keys($categoryIds);

        // Get all categories used in products at once
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', $categoryIds);
        $categories = $collection->getItems();

        // Add parent categories to products
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $newCategoryIds = $product->getCategoryIds();
            foreach ($product->getCategoryIds() as $categoryId) {
                $parentIds = $categories[(int)$categoryId]->getParentIds();
                array_push($newCategoryIds, ...$parentIds);
            }
            $newCategoryIds = array_unique($newCategoryIds);
            $product->setCategoryIds($newCategoryIds);
        }
    }
}
