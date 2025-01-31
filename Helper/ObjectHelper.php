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

namespace HawkSearch\EsIndexing\Helper;

use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Api\SortOrderFactory;

/**
 * @api
 * @since 0.8.0
 */
class ObjectHelper
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private FilterFactory $filterFactory;
    private SortOrderFactory $sortOrderFactory;
    private FilterGroupBuilder $filterGroupBuilder;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterFactory $filterFactory,
        SortOrderFactory $sortOrderFactory,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterFactory = $filterFactory;
        $this->sortOrderFactory = $sortOrderFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @phpstan-type keyFilterGroups SearchCriteria::FILTER_GROUPS
     * @phpstan-type keySortOrders SearchCriteria::SORT_ORDERS
     * @phpstan-type keyPageSize SearchCriteria::PAGE_SIZE
     * @phpstan-type keyCurrentPage SearchCriteria::CURRENT_PAGE
     * @param array{
     *              keyFilterGroups: array<mixed>,
     *              keySortOrders: array<mixed>,
     *              keyPageSize: ?int,
     *              keyCurrentPage: ?int,
     *        } $data
     * @return SearchCriteria
     */
    public function convertArrayToSearchCriteriaObject(array $data)
    {
        foreach ($data as $key => $value) {
            $setMethodName = 'set' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key);
            if (method_exists($this->searchCriteriaBuilder, $setMethodName)) {
                $this->searchCriteriaBuilder->{$setMethodName}($this->buildSearchCriteriaValue($key, $value));
            }
            unset($data[$key]);
        }

        /**
         * We need to loop over the rest of items again and add them as filters to make sure that
         * filter_groups parameter do not overwrite an already added filter
         */
        foreach ($data as $key => $value) {
            $this->searchCriteriaBuilder->addFilter($key, $value);
        }

        return $this->searchCriteriaBuilder->create();
    }

    /**
     * @return mixed
     */
    protected function buildSearchCriteriaValue(string $param, mixed $value)
    {
        switch ($param) {
            case 'filter_groups':
                $filterGroups = [];
                foreach ($value as $filterGroup) {
                    if (empty($filterGroup['filters'])) {
                        continue;
                    }

                    foreach ($filterGroup['filters'] as $filter) {
                        $this->filterGroupBuilder->addFilter($this->filterFactory->create(['data' => $filter]));
                    }
                    $filterGroups[] = $this->filterGroupBuilder->create();
                }
                $resultValue = $filterGroups;
                break;
            case 'sort_orders':
                $sortOrders = [];
                foreach ($value as $sortOrder) {
                    $sortOrders[] = $this->sortOrderFactory->create(['data' => $sortOrder]);
                }
                $resultValue = $sortOrders;
                break;
            default:
                $resultValue = $value;
        }

        return $resultValue;
    }

    /**
     * @return mixed
     */
    public function getSearchCriteriaFilterValue(SearchCriteriaInterface $searchCriteria, string $filterField)
    {
        $value = null;
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === $filterField) {
                    $value = $filter->getValue();
                }
            }
        }

        return $value;
    }

    /**
     * Validate array values to be an instance of a class
     * Used as array_walk callable
     *
     * @return void
     * @throws \InvalidArgumentException
     * @todo remove $key argument, use a closure for array_walk explicitely and use this method implicitely in the
     *     closure
     */
    public static function validateObjectValue(object $item, int $key, string $className)
    {
        if (!$item instanceof $className) {
            throw new \InvalidArgumentException(
                __('Array element value with key %1 is not an instance of %2 interface', $key, $className)->render()
            );
        }
    }
}
