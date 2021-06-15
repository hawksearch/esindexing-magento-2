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
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;

class HierarchyItemsProvider implements ItemsProviderInterface
{
    /**
     * @var CategoryResource
     */
    private $categoryResource;

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
     * @param CategoryResource $categoryResource
     * @param CategoryHelper $categoryHelper
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryResource $categoryResource,
        CategoryHelper $categoryHelper,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryResource = $categoryResource;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritDoc
     * @return CategoryInterface[]
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
        $storeParentCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        /**
         * Check if parent node of the store still exists
         */
        /* @var $category CategoryModel */
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $storeParentCategoryId);

        if (!$category->getId()) {
            return [];
        }

        $pathRegexGroups = [
            $category->getPath() . "$",
            $category->getPath() . "/"
        ];
        $pathFilterRegex = "(" . implode('|', $pathRegexGroups) . ")";
        $categories = $category->getCategories($category->getParentId(), 0, false, true, false);
        $categories->addPathFilter($pathFilterRegex);

        if ($entityIds && count($entityIds) > 0) {
            $categories->addIdFilter($entityIds);
        }
        $categories->setCurPage($currentPage)
            ->setPageSize($pageSize);

        return $categories->getItems();
    }
}
