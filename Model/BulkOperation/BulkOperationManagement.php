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

namespace HawkSearch\EsIndexing\Model\BulkOperation;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationSearchResultsInterface;
use Magento\AsynchronousOperations\Api\OperationRepositoryInterface;
use Magento\AsynchronousOperations\Model\ResourceModel\Bulk\Collection as BulkCollection;
use Magento\AsynchronousOperations\Model\ResourceModel\Bulk\CollectionFactory as BulkCollectionFactory;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\Collection as OperationCollection;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory as OperationCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Bulk\OperationInterface as BulkOperationInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 * @since 0.8.0
 */
class BulkOperationManagement
{
    public const OPERATION_TOPIC_PREFIX = 'hawksearch.indexing.';

    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;
    private OperationRepositoryInterface $operationRepository;
    private OperationCollectionFactory $operationCollectionFactory;
    private BulkCollectionFactory $bulkCollectionFactory;

    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        OperationRepositoryInterface $operationRepository,
        OperationCollectionFactory $operationCollectionFactory,
        BulkCollectionFactory $bulkCollectionFactory
    )
    {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->operationRepository = $operationRepository;
        $this->operationCollectionFactory = $operationCollectionFactory;
        $this->bulkCollectionFactory = $bulkCollectionFactory;
    }

    /**
     * @return OperationInterface
     * @throws NoSuchEntityException
     */
    public function getOperationByBulkAndKey(string $bulkUuid, int $operationKey)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            BulkOperationInterface::BULK_ID,
            $bulkUuid
        )->addFilter(
            BulkOperationInterface::ID,
            $operationKey
        );

        $searchCriteria = $searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $operations = $this->operationRepository->getList($searchCriteria)->getItems();

        if (count($operations)) {
            return current($operations);
        }

        throw new NoSuchEntityException(
            __(
                'No such operation Bulk UUID: %1, key: %2',
                $bulkUuid,
                $operationKey
            )
        );
    }

    /**
     * @return OperationSearchResultsInterface
     */
    public function getOperationsByBulkUuidAndStatus(string $bulkUuid, int $status)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            BulkOperationInterface::BULK_ID,
            $bulkUuid
        )->addFilter(
            BulkOperationInterface::STATUS,
            $status
        );
        $searchCriteria = $searchCriteriaBuilder->create();

        return $this->operationRepository->getList($searchCriteria);
    }

    /**
     * @return OperationSearchResultsInterface
     */
    public function getOperationsByBulkUuid(string $bulkUuid)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            BulkOperationInterface::BULK_ID,
            $bulkUuid
        );
        $searchCriteria = $searchCriteriaBuilder->create();

        return $this->operationRepository->getList($searchCriteria);
    }

    /**
     * Get bulks related to Hawksearch operations only
     *
     * @return BulkCollection
     */
    public function getAllBulksCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            BulkOperationInterface::TOPIC_NAME,
            self::OPERATION_TOPIC_PREFIX . '%',
            'like'
        );

        /** @var OperationCollection $collection */
        $collection = $this->operationCollectionFactory->create();

        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(
                [
                    'id' => 'bulk_uuid',
                    'name' => 'bulk_uuid'
                ]
            )
            ->where(
                "main_table.topic_name LIKE (?)",
                self::OPERATION_TOPIC_PREFIX . '%'
            )
            ->group('bulk_uuid');

        $bulkUuids = $collection->getConnection()->fetchCol($collection->getSelect());

        /** @var BulkCollection $collection */
        $collection = $this->bulkCollectionFactory->create();
        $collection->getSelect()
            ->columns(
                [
                    'id' => 'id',
                    'name' => 'uuid'
                ]
            );
        $collection->addFieldToFilter('uuid', ['in' => $bulkUuids]);

        return $collection;
    }
}
