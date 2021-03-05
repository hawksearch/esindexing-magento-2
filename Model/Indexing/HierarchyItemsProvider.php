<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\Category as ModelCategory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;

class HierarchyItemsProvider implements ItemsProviderInterface
{
    /**
     * @var CategoryModel
     */
    private $categoryModel;

    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;


    /**
     * HierarchyItemsProvider constructor.
     * @param CategoryModel $categoryModel
     * @param CategoryHelper $categoryHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryModel $categoryModel,
        CategoryHelper $categoryHelper,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryModel = $categoryModel;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritDoc
     * @return ProductInterface[]
     */
    public function getItems($storeId, $entityIds = null, $currentPage = 1, $pageSize = 0)
    {
        return $this->getCategoryCollection($storeId, $entityIds, $currentPage, $pageSize);
    }

    /**
     * @param int $storeId
     * @param array|null $entityIds
     * @param int $currentPage
     * @param int $pageSize
     * @return CategoryInterface[]
     */
    protected function getCategoryCollection($storeId, $entityIds = null, $currentPage = 1, $pageSize = 0): array
    {
        $parentId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->categoryFactory->create();
        /* @var $category ModelCategory */
        if (!$category->checkId($parentId)) {
            return [];
        }

        //$storeRootCategory =
        $categories = $this->categoryModel->getCategories($parentId, 0, false, true, false);
        $categories->addIdFilter($parentId);

        /*if ($productIds && count($productIds) > 0) {
            $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in');
        }*/


        /*$searchCriteria->setCurrentPage($currentPage)
            ->setPageSize($pageSize);*/

        return $categories->getItems();
        //return $this->productRepository->getList($searchCriteria)->getItems();
    }
}
