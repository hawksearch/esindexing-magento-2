<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class ContentPageItemsProvider implements ItemsProviderInterface
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * ContentPageItemsProvider constructor.
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    )
    {
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getItems(int $storeId, $entityIds = null, $currentPage = 1, $pageSize = 0)
    {
        return $this->getPageCollection($storeId, $entityIds, $currentPage, $pageSize);
    }

    /**
     * @param int $storeId
     * @param array|null $entityIds
     * @param int $currentPage
     * @param int $pageSize
     * @return PageInterface[]
     * @throws LocalizedException
     */
    protected function getPageCollection($storeId, $entityIds = null, $currentPage = 1, $pageSize = 0)
    {
        $this->searchCriteriaBuilder->addFilter(
            'store_id',
            [$storeId, Store::DEFAULT_STORE_ID],
            'in'
        );

        if ($entityIds && count($entityIds) > 0) {
            $this->searchCriteriaBuilder->addFilter('page_id', $entityIds, 'in');
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setCurrentPage($currentPage)
            ->setPageSize($pageSize);

        return $this->pageRepository->getList($searchCriteria)->getItems();
    }
}
