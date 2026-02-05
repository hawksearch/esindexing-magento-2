<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Cron;

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterfaceFactory;
use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use HawkSearch\EsIndexing\Model\Config\Indexing\FailureRecovery as FailureRecoveryConfig;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Model\BulkSummary;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\Collection as OperationCollection;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory as OperationCollectionFactory;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\MessageEncoder;
use Magento\Framework\MessageQueue\MessageValidator;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class RetryFailedOperations
{
    /**
     * Default error code
     */
    private const ERROR_CODE = 0;
    private MessageEncoder $messageEncoder;
    private MessageValidator $messageValidator;
    private BulkOperationManagement $bulkOperationManagement;
    private OperationManagementInterface $operationManagement;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private QueueOperationDataInterfaceFactory $operationDataFactory;
    private BulkManagementInterface $bulkRetryManagement;
    private DateTime $dateTime;
    private Operation $operationResource;
    private OperationCollectionFactory $operationCollectionFactory;
    private FailureRecoveryConfig $failureRecoveryConfig;
    private int $errorCode;

    public function __construct(
        MessageEncoder $messageEncoder,
        MessageValidator $messageValidator,
        BulkOperationManagement $bulkOperationManagement,
        OperationManagementInterface $operationManagement,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        QueueOperationDataInterfaceFactory $operationDataFactory,
        BulkManagementInterface $bulkRetryManagement,
        DateTime $dateTime,
        Operation $operationResource,
        OperationCollectionFactory $operationCollectionFactory,
        FailureRecoveryConfig $failureRecoveryConfig,
        int $errorCode = self::ERROR_CODE

    ) {
        $this->bulkOperationManagement = $bulkOperationManagement;
        $this->messageEncoder = $messageEncoder;
        $this->messageValidator = $messageValidator;
        $this->operationManagement = $operationManagement;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->operationDataFactory = $operationDataFactory;
        $this->bulkRetryManagement = $bulkRetryManagement;
        $this->dateTime = $dateTime;
        $this->operationResource = $operationResource;
        $this->operationCollectionFactory = $operationCollectionFactory;
        $this->failureRecoveryConfig = $failureRecoveryConfig;
        $this->errorCode = $errorCode;
    }

    /**
     * Automatically retry failed and not started operations.
     * When trials limit is reached then operation is rejected and can be recovered manually
     *
     * @throws LocalizedException
     */
    public function execute(): void
    {
        if (!$this->failureRecoveryConfig->isEnabled()) {
            return;
        }

        $operationsToProcess = array_merge(
            $this->getFailedOperations(),
            $this->getNotStartedOperations()
        );
        $retryOperations = [];

        foreach ($operationsToProcess as $operation) {
            if ($this->retryOperation($operation)) {
                if (!isset($retryOperations[$operation->getBulkUuid()])) {
                    $retryOperations[$operation->getBulkUuid()] = [];
                }
                $retryOperations[$operation->getBulkUuid()][] = $operation;
            }
        }

        foreach ($retryOperations as $bulkUuid => $operations) {
            $this->bulkRetryManagement->scheduleBulk(
                $bulkUuid,
                $operations,
                ''
            );
        }
    }

    /**
     * Get operations with failed statuses
     *
     * @return OperationInterface[]
     */
    protected function getFailedOperations(): array
    {
        /** @var OperationCollection $collection */
        $collection = $this->operationCollectionFactory->create();

        $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                [
                    '*',
                    'operation_key' => 'CONCAT(bulk_uuid,"_",operation_key)',
                    'operation_key_orig' => 'operation_key',
                ]
            )
            ->where(
                "main_table.topic_name LIKE (?)",
                BulkOperationManagement::OPERATION_TOPIC_PREFIX . '%'
            )->where(
                OperationInterface::STATUS . ' IN (?)',
                [OperationInterface::STATUS_TYPE_RETRIABLY_FAILED, OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED]
            );

        $items = $collection->getItems();
        foreach ($items as $item) {
            $item->setData('operation_key', $item->getData('operation_key_orig'));
        }

        /** @var OperationInterface[] */
        return $collection->getItems();
    }

    /**
     * Get operations with open status
     *
     * @return OperationInterface[]
     * @throws LocalizedException
     * @see \Magento\AsynchronousOperations\Cron\MarkIncompleteOperationsAsFailed
     */
    protected function getNotStartedOperations(): array
    {
        $bulks = $this->bulkOperationManagement->getAllBulksCollection();
        $now = $this->dateTime->gmtTimestamp();

        $bulkUuidSet = [];
        /** @var BulkSummary $bulk */
        foreach ($bulks as $bulk) {
            if ($this->dateTime->gmtTimestamp($bulk->getStartTime()) <= ($now - $this->failureRecoveryConfig->getMaximumOpenDelay())) {
                $bulkUuidSet[] = $bulk->getBulkId();
            }
        }

        /** @var OperationCollection $collection */
        $collection = $this->operationCollectionFactory->create();

        $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                [
                    '*',
                    'operation_key' => 'CONCAT(bulk_uuid,"_",operation_key)',
                    'operation_key_orig' => 'operation_key',
                ]
            )->where(
                OperationInterface::STATUS . ' IN (?)',
                [OperationInterface::STATUS_TYPE_OPEN]
            )->where(
                OperationInterface::BULK_ID . ' IN (?)',
                $bulkUuidSet
            );

        $connection = $this->operationResource->getConnection();
        if ($connection->tableColumnExists($this->operationResource->getMainTable(), 'started_at')) {
            $collection->getSelect()->where(
                'started_at IS NULL'
            );
        }

        $items = $collection->getItems();
        foreach ($items as $item) {
            $item->setData('operation_key', $item->getData('operation_key_orig'));
        }

        /** @var OperationInterface[] */
        return $collection->getItems();
    }

    /**
     * @throws LocalizedException
     */
    protected function retryOperation(OperationInterface $operation): bool
    {
        try {
            $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);
        } catch (LocalizedException|\InvalidArgumentException $exception) {
            $this->rejectOperation($operation, $exception->getCode(), $exception->getMessage());
            return false;
        }

        $data = $this->serializer->unserialize($operation->getSerializedData());
        $entityParams = $this->messageEncoder->decode($operation->getTopicName(), $data['meta_information']);
        $operationData = current($entityParams);

        $numberOfTrials = $this->getOperationTrials($operationData);

        if ($numberOfTrials < $this->failureRecoveryConfig->getMaximumRetries()) {
            $operationData = $this->increaseOperationTrials($operationData);
            $data['meta_information'] = $this->messageEncoder->encode($operation->getTopicName(), [$operationData]);
            $serializedData = $this->serializer->serialize($data);
            $operation->setSerializedData($serializedData);
            return true;
        }

        $this->rejectOperation($operation, $this->errorCode, "Limit of retry attempts has been reached");
        return false;
    }

    /**
     * Get number of retry attempts for operation
     */
    protected function getOperationTrials(QueueOperationDataInterface $operation): int
    {
        $data = $this->serializer->unserialize($operation->getData());

        return $data['number_of_trials'] ?? 0;
    }

    /**
     * Increase number of retry attempts for operation
     */
    protected function increaseOperationTrials(QueueOperationDataInterface $operationData): QueueOperationDataInterface
    {
        $data = $this->serializer->unserialize($operationData->getData());
        $data['number_of_trials'] = ($data['number_of_trials'] ?? 0) + 1;

        return $this->operationDataFactory->create(['data' => $this->serializer->serialize($data)]);
    }

    /**
     * Change operation status to rejected
     */
    protected function rejectOperation(
        OperationInterface $operation,
        ?int $errorCode = null,
        ?string $message = null
    ): bool {
        $this->logger->critical(__('Message has been rejected: %1', $message));

        return $this->operationManagement->changeOperationStatus(
            $operation->getBulkUuid(),
            $operation->getId(),
            OperationInterface::STATUS_TYPE_REJECTED,
            $errorCode,
            $message,
            $operation->getSerializedData()
        );
    }
}
