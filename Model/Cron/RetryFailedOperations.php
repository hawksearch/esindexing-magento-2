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
use Magento\AsynchronousOperations\Model\BulkSummary;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
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
    public const NUMBER_OF_TRIALS = 3;
    public const BULK_AGE = 86400;

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

    public function __construct(
        MessageEncoder $messageEncoder,
        MessageValidator $messageValidator,
        BulkOperationManagement $bulkOperationManagement,
        OperationManagementInterface $operationManagement,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        QueueOperationDataInterfaceFactory $operationDataFactory,
        BulkManagementInterface $bulkRetryManagement,
        DateTime $dateTime

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
    }

    /**
     * Automatically retry failed operations.
     * When NUMBER_OF_TRIALS limit is reached then operation is rejected and can be retried manually
     *
     * @return void
     */
    public function execute(): void
    {
        $bulks = $this->bulkOperationManagement->getAllBulksCollection();
        $now = $this->dateTime->gmtTimestamp();

        /** @var BulkSummary  $bulk */
        foreach ($bulks as $bulk) {

            if ($this->dateTime->gmtTimestamp($bulk->getStartTime()) < ($now - self::BULK_AGE)) {
                continue;
            }

            $retryOperations = [];
            $failedOperations = array_merge(
                $this->bulkOperationManagement->getOperationsByBulkUuidAndStatus(
                    $bulk->getBulkId(),
                    OperationInterface::STATUS_TYPE_RETRIABLY_FAILED
                )->getItems(),
                $this->bulkOperationManagement->getOperationsByBulkUuidAndStatus(
                    $bulk->getBulkId(),
                    OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED
                )->getItems()
            );

            foreach ($failedOperations as $operation) {
                if ($this->retryOperation($operation)) {
                    $retryOperations[] = $operation;
                }
            }

            $this->bulkRetryManagement->scheduleBulk(
                $bulk->getBulkId(),
                $retryOperations,
                $bulk->getDescription(),
                $bulk->getUserId()
            );
        }
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

        if ($numberOfTrials < self::NUMBER_OF_TRIALS) {
            $operationData = $this->increaseOperationTrials($operationData);
            $data['meta_information'] = $this->messageEncoder->encode($operation->getTopicName(), [$operationData]);
            $serializedData = $this->serializer->serialize($data);
            $operation->setSerializedData($serializedData);
            return true;
        }

        $this->rejectOperation($operation, null, "Limit of retry attempts has been reached");
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
     * Chahne operation status to Rejected
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
