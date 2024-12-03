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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\LandingPage;

use HawkSearch\EsIndexing\Model\Indexing\ItemsDataProviderInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ItemsDataProvider implements ItemsDataProviderInterface
{
    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * HierarchyItems constructor.
     * @param CategoryResource $categoryResource
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryResource $categoryResource,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryResource = $categoryResource;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritDoc
     * @return CategoryInterface[]
     */
    public function getItems(int $storeId, ?array $entityIds = null, int $currentPage = 1, int $pageSize = 0)
    {
        return $this->getCategoryCollection($storeId, $entityIds, $currentPage, $pageSize);
    }

    /**
     * @param int $storeId
     * @param array<int>|null $entityIds
     * @param int $currentPage
     * @param int $pageSize
     * @return CategoryInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCategoryCollection(int $storeId, ?array $entityIds = null, int $currentPage = 1, int $pageSize = 0): array
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
            //$category->getPath() . "$",
            $category->getPath() . "/"
        ];
        $pathFilterRegex = "(" . implode('|', $pathRegexGroups) . ")";
        $categories = $category->getCategories($category->getParentId(), 0, false, true, false);
        $categories->addPathFilter($pathFilterRegex)
            ->addAttributeToSelect('name')
            ->addAttributeToSort('entity_id')
            ->addAttributeToSort('parent_id')
            ->addAttributeToSort('position');

        if ($entityIds && count($entityIds) > 0) {
            $categories->addIdFilter($entityIds);
        }
        $categories->setCurPage($currentPage)
            ->setPageSize($pageSize);

        return $categories->getItems();
    }
}
