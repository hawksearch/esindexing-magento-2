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

namespace HawkSearch\EsIndexing\Model\Cron;

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterfaceFactory;
use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\Collection as OperationCollection;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory as OperationCollectionFactory;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\MessageEncoder;
use Magento\Framework\MessageQueue\MessageValidator;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class RetryFailedOperations
{
    /**
     * Default operation number of trials. Default to 3
     */
    private const DEFAULT_OPERATION_NUMBER_OF_TRIALS = 3;

    /**
     * Default maximum processing time of not started operations. Default to 12h
     */
    private const DEFAULT_OPERATION_NOT_STARTED_MAX_PROCESSING_TIME = 43200;

    /**
     * Default error code
     */
    private const ERROR_CODE = 0;

    /**
     * @var MessageEncoder
     */
    private $messageEncoder;

    /**
     * @var MessageValidator
     */
    private $messageValidator;

    /**
     * @var BulkOperationManagement
     */
    private $bulkOperationManagement;

    /**
     * @var OperationManagementInterface
     */
    private $operationManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var QueueOperationDataInterfaceFactory
     */
    private $operationDataFactory;

    /**
     * @var BulkManagementInterface
     */
    private $bulkRetryManagement;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Operation
     */
    private $operationResource;

    /**
     * @var OperationCollectionFactory
     */
    private $operationCollectionFactory;

    /**
     * @var int
     */
    private $numberOfTrials;

    /**
     * @var int
     */
    private $notStartedMaxProcessingTime;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @param MessageEncoder $messageEncoder
     * @param MessageValidator $messageValidator
     * @param BulkOperationManagement $bulkOperationManagement
     * @param OperationManagementInterface $operationManagement
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param QueueOperationDataInterfaceFactory $operationDataFactory
     * @param BulkManagementInterface $bulkRetryManagement
     * @param DateTime $dateTime
     * @param Operation $operationResource
     * @param OperationCollectionFactory $operationCollectionFactory
     * @param int $numberOfTrials
     * @param int $notStartedMaxProcessingTime
     * @param int $errorCode
     */
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
        int $numberOfTrials = self::DEFAULT_OPERATION_NUMBER_OF_TRIALS,
        int $notStartedMaxProcessingTime = self::DEFAULT_OPERATION_NOT_STARTED_MAX_PROCESSING_TIME,
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
        $this->numberOfTrials = $numberOfTrials;
        $this->notStartedMaxProcessingTime = $notStartedMaxProcessingTime;
        $this->errorCode = $errorCode;
    }

    /**
     * Automatically retry failed and not started operations.
     * When numberOfTrials limit is reached then operation is rejected and can be retried manually
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
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
     * @return OperationInterface[]
     */
    protected function getFailedOperations()
    {
        /** @var OperationCollection $collection */
        $collection = $this->operationCollectionFactory->create();

        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
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
        return $collection->getItems();
    }

    /**
     *
     *
     * @return OperationInterface[]
     * @throws LocalizedException
     * @see \Magento\AsynchronousOperations\Cron\MarkIncompleteOperationsAsFailed
     */
    protected function getNotStartedOperations()
    {
        $bulks = $this->bulkOperationManagement->getAllBulksCollection();
        $now = $this->dateTime->gmtTimestamp();

        $bulkUuidSet = [];
        foreach ($bulks as $bulk) {
            if ($this->dateTime->gmtTimestamp($bulk->getStartTime()) < ($now - $this->notStartedMaxProcessingTime)) {
                $bulkUuidSet[] = $bulk->getBulkId();
            }
        }

        /** @var OperationCollection $collection */
        $collection = $this->operationCollectionFactory->create();

        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
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
        return $collection->getItems();
    }

    /**
     * Mark operation as retriable
     *
     * @param OperationInterface $operation
     * @return bool
     * @throws LocalizedException
     */
    protected function retryOperation(OperationInterface $operation)
    {
        try {
            $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);
        } catch (LocalizedException | \InvalidArgumentException $exception) {
            $this->rejectOperation($operation, $exception->getCode(), $exception->getMessage());
            return false;
        }

        $data = $this->serializer->unserialize($operation->getSerializedData());
        $entityParams = $this->messageEncoder->decode($operation->getTopicName(), $data['meta_information']);
        $operationData = current($entityParams);

        $numberOfTrials = $this->getOperationTrials($operationData);

        if ($numberOfTrials < $this->numberOfTrials) {
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
     *
     * @param QueueOperationDataInterface $operation
     * @return int
     */
    protected function getOperationTrials(QueueOperationDataInterface $operation)
    {
        $data = $this->serializer->unserialize($operation->getData());

        return $data['number_of_trials'] ?? 0;
    }

    /**
     * Increase number of retry attempts for operation
     *
     * @param QueueOperationDataInterface $operationData
     * @return QueueOperationDataInterface
     */
    protected function increaseOperationTrials(QueueOperationDataInterface $operationData)
    {
        $data = $this->serializer->unserialize($operationData->getData());
        $data['number_of_trials'] =  ($data['number_of_trials'] ?? 0) + 1;

        return $this->operationDataFactory->create(['data' => $this->serializer->serialize($data)]);
    }

    /**
     * Change operation status to rejected
     *
     * @param OperationInterface $operation
     * @param int|null $errorCode
     * @param string|null $message
     * @return bool
     */
    protected function rejectOperation(OperationInterface  $operation, $errorCode = null, $message = null)
    {
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
