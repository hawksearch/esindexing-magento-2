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

use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterface;
use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterfaceFactory;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\BulkPublisherInterface;
use Psr\Log\LoggerInterface;

class RetryBulkManagement implements BulkManagementInterface
{
    /**
     * @var BulkSummaryInterfaceFactory
     */
    private $bulkSummaryFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var OperationManagementInterface
     */
    private $operationManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var BulkPublisherInterface
     */
    private $publisher;

    /**
     * RetryBulkManagement constructor.
     * @param BulkSummaryInterfaceFactory $bulkSummaryFactory
     * @param EntityManager $entityManager
     * @param OperationManagementInterface $operationManagement
     * @param LoggerInterface $logger
     * @param BulkPublisherInterface $publisher
     */
    public function __construct(
        BulkSummaryInterfaceFactory $bulkSummaryFactory,
        EntityManager $entityManager,
        OperationManagementInterface $operationManagement,
        LoggerInterface $logger,
        BulkPublisherInterface $publisher
    )
    {
        $this->bulkSummaryFactory = $bulkSummaryFactory;
        $this->entityManager = $entityManager;
        $this->operationManagement = $operationManagement;
        $this->logger = $logger;
        $this->publisher = $publisher;
    }

    /**
     * It is used to reschedule bulk operations
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function scheduleBulk($bulkUuid, array $operations, $description, $userId = null)
    {
        try {
            /** @var BulkSummaryInterface $bulkSummary */
            $bulkSummary = $this->bulkSummaryFactory->create();
            $this->entityManager->load($bulkSummary, $bulkUuid);
            if (!$bulkSummary->getBulkId()) {
                throw new NoSuchEntityException(__('Bulk is not found'));
            }
        } catch (\Exception $exception) {
            //re-throw exception
            $this->logger->critical($exception->getMessage());
            throw $exception;
        }

        foreach ($operations as $operation) {
            $this->operationManagement->changeOperationStatus(
                $operation->getBulkUuid(),
                $operation->getId(),
                OperationInterface::STATUS_TYPE_OPEN,
                $operation->getErrorCode(),
                $operation->getResultMessage(),
                $operation->getSerializedData()
            );
        }
        $this->publishOperations($operations);

        if (!$operations) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteBulk($bulkId)
    {
        throw new \LogicException('Method is not supported.');
    }

    /**
     * Publish list of operations to the corresponding message queues.
     *
     * @param array $operations
     * @return void
     */
    private function publishOperations(array $operations)
    {
        $operationsByTopics = [];
        foreach ($operations as $operation) {
            $operationsByTopics[$operation->getTopicName()][] = $operation;
        }
        foreach ($operationsByTopics as $topicName => $operations) {
            $this->publisher->publish($topicName, $operations);
        }
    }
}
