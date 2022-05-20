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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

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
     * ProductItemsProvider constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Visibility $visibility
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Visibility $visibility,
        FilterBuilder $filterBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->visibility = $visibility;
        $this->filterBuilder = $filterBuilder;
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

        return $this->productRepository->getList($searchCriteria)->getItems();
    }
}
