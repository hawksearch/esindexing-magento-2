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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\OperationRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

class IndexingOperationValidator
{
    private const OPERATION_TOPIC_PREFIX = 'hawksearch.indexing.';

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var OperationRepositoryInterface
     */
    private $operationRepository;

    /**
     * IndexingOperationValidator constructor.
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param OperationRepositoryInterface $operationRepository
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        OperationRepositoryInterface $operationRepository
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     */
    public function isOperationTopicAllowed(OperationInterface $operation)
    {
        return strpos($operation->getTopicName(), self::OPERATION_TOPIC_PREFIX) === 0;
    }

    /**
     * @param OperationInterface $operation
     * @return bool|void
     * @throws NoSuchEntityException
     */
    protected function isPrevOperationComplete(OperationInterface $operation)
    {
        $prevOperationKey = (int)$operation->getId() === 0 ? null : (int)$operation->getId() - 1;
        $prevOperationStatus = $this->getOperationByBulkAndKey(
            $operation->getBulkUuid(),
            $prevOperationKey
        )->getStatus();

        return $prevOperationKey !== null
            ? $prevOperationStatus == OperationInterface::STATUS_TYPE_COMPLETE
            //this is the first operation in bulk
            : true;
    }

    /**
     * @param $bulkUuid
     * @param $operationKey
     * @return OperationInterface
     * @throws NoSuchEntityException
     */
    protected function getOperationByBulkAndKey($bulkUuid, $operationKey)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            OperationInterface::BULK_ID,
            $bulkUuid
        )->addFilter(
            OperationInterface::ID,
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
     * @param string $bulkUuid
     * @return bool
     * @todo Check bulk consistency: operations order is correct,
     * @todo number of operation is eq to bulk operations count,
     * @todo all bulk operations are hawksearch operations
     */
    protected function isBulkConsistent($bulkUuid)
    {
        return true;
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function isValidOperation(OperationInterface $operation)
    {
        if ($operation->getStatus() == OperationInterface::STATUS_TYPE_COMPLETE) {
            throw new NotFoundException(
                __(
                    'Operation was already processed Bulk UUID: %1, key: %2',
                    $operation->getBulkUuid(),
                    $operation->getId()
                )
            );
        }

        return $this->isBulkConsistent($operation->getBulkUuid())
            && $this->isPrevOperationComplete($operation);
    }
}
