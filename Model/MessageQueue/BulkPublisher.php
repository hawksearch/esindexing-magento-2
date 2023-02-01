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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterfaceFactory;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use Magento\AsynchronousOperations\Api\SaveMultipleOperationsInterface;
use Magento\AsynchronousOperations\Model\OperationRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\BulkException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class BulkPublisher implements BulkPublisherInterface
{
    public const DEFAULT_BULK_DESCRIPTION = 'Hawksearch indexing bulk operation';

    /**
     * @var array
     */
    private $messages;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var OperationRepositoryInterface
     */
    private $operationRepository;

    /**
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveMultipleOperationsInterface
     */
    private $saveMultipleOperations;

    /**
     * @var QueueOperationDataInterfaceFactory
     */
    private $queueOperationDataFactory;

    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var string
     */
    private $bulkDescription;

    /**
     * Publisher constructor.
     * @param SerializerInterface $serializer
     * @param OperationRepositoryInterface $operationRepository
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param SaveMultipleOperationsInterface $saveMultipleOperations
     * @param QueueOperationDataInterfaceFactory $queueOperationDataFactory
     * @param string|null $bulkDescription
     */
    public function __construct(
        SerializerInterface $serializer,
        OperationRepositoryInterface $operationRepository,
        IdentityGeneratorInterface $identityService,
        BulkManagementInterface $bulkManagement,
        UserContextInterface $userContext,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        SaveMultipleOperationsInterface $saveMultipleOperations,
        QueueOperationDataInterfaceFactory $queueOperationDataFactory,
        IndexManagementInterface $indexManagement,
        string $bulkDescription = null
    ) {
        $this->serializer = $serializer;
        $this->operationRepository = $operationRepository;
        $this->identityService = $identityService;
        $this->bulkManagement = $bulkManagement;
        $this->userContext = $userContext;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->saveMultipleOperations = $saveMultipleOperations;
        $this->queueOperationDataFactory = $queueOperationDataFactory;
        $this->indexManagement = $indexManagement;
        $this->bulkDescription = $bulkDescription;
    }


    /**
     * @inheritDoc
     */
    public function addMessage($topicName, $data)
    {
        $this->messages[] =
            [
                'topic' => $topicName,
                'data' => $this->updateApplicationHeaders($data),
            ];

        return $this;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws \Exception
     */
    public function publish()
    {
        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = $this->bulkDescription ?? static::DEFAULT_BULK_DESCRIPTION;
        $userId = $this->userContext->getUserId();

        /** create new bulk without operations */
        if (!$this->bulkManagement->scheduleBulk($bulkUuid, [], $bulkDescription, $userId)) {
            throw new LocalizedException(
                __('Something went wrong while scheduling bulk operation request.')
            );
        }

        $operations = [];
        $bulkException = new BulkException();
        foreach (($this->messages ?? []) as $operationId => $topicMessage) {
            try {
                $operationData = $this->queueOperationDataFactory->create(
                    [
                        'data' => $this->serializer->serialize($topicMessage['data']),
                    ]
                );
                $operations[] = $this->operationRepository->create(
                    $topicMessage['topic'],
                    [$operationData],
                    $bulkUuid,
                    $operationId
                );
            } catch (\Exception $exception) {
                $this->logger->error($exception);
                $bulkException->addException(
                    new LocalizedException(
                        __(
                            'Error processing %key element of input data for bulk UID %uid',
                            ['key' => $operationId, 'uid' => $bulkUuid]
                        ),
                        $exception
                    )
                );
            }
        }

        $this->saveMultipleOperations->execute($operations);
        if (!$this->bulkManagement->scheduleBulk($bulkUuid, $operations, $bulkDescription, $userId)) {
            try {
                $this->bulkManagement->deleteBulk($bulkUuid);
            } finally {
                throw new LocalizedException(
                    __('Something went wrong while processing the request.')
                );
            }
        }

        if ($bulkException->wasErrorAdded()) {
            throw $bulkException;
        } else {
            $this->messages = null;
        }
    }

    /**
     * Set current store_id in messageData['application_headers']
     * so consumer may check store_id and execute operation in correct store scope.
     * Prevent publishing inconsistent messages because of store_id not defined or wrong.
     * Set other operation global data
     * @param array $data
     */
    private function updateApplicationHeaders(array $data)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $isFullReindex = $data['full_reindex'] ?? false;
            $indexName = $this->indexManagement->getIndexName(!$isFullReindex);
        } catch (NoSuchEntityException $e) {
            $errorMessage = sprintf(
                "Can't get current storeId and inject to the message queue. Error %s.",
                $e->getMessage()
            );
            $this->logger->error($errorMessage);
            throw new \LogicException($errorMessage);
        }

        $data['application_headers'] = $data['application_headers'] ?? [];
        $data['application_headers']['store_id'] = $storeId;
        $data['application_headers']['index'] = $indexName;
        $data['application_headers']['full_reindex'] = $isFullReindex;

        return $data;
    }
}
