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

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterfaceFactory;
use Magento\AsynchronousOperations\Api\SaveMultipleOperationsInterface;
use Magento\AsynchronousOperations\Model\OperationRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\BulkException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * @todo deprecate extending AbstractSimpleObject
 */
class BulkPublisher extends AbstractSimpleObject implements BulkPublisherInterface
{
    public const DEFAULT_BULK_DESCRIPTION = 'Hawksearch indexing bulk operation';

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var OperationRepositoryInterface
     */
    private OperationRepositoryInterface $operationRepository;

    /**
     * @var IdentityGeneratorInterface
     */
    private IdentityGeneratorInterface $identityService;

    /**
     * @var BulkManagementInterface
     */
    private BulkManagementInterface $bulkManagement;

    /**
     * @var UserContextInterface
     */
    private UserContextInterface $userContext;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var SaveMultipleOperationsInterface
     */
    private SaveMultipleOperationsInterface $saveMultipleOperations;

    /**
     * @var QueueOperationDataInterfaceFactory
     */
    private QueueOperationDataInterfaceFactory $queueOperationDataFactory;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * @var string
     */
    private string $bulkDescription;

    /**
     * @param SerializerInterface $serializer
     * @param OperationRepositoryInterface $operationRepository
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     * @param LoggerFactoryInterface $loggerFactory
     * @param SaveMultipleOperationsInterface $saveMultipleOperations
     * @param QueueOperationDataInterfaceFactory $queueOperationDataFactory
     * @param MessageManagerInterface $messageManager
     * @param string|null $bulkDescription
     * @param array<array-key, mixed> $data
     */
    public function __construct(
        SerializerInterface $serializer,
        OperationRepositoryInterface $operationRepository,
        IdentityGeneratorInterface $identityService,
        BulkManagementInterface $bulkManagement,
        UserContextInterface $userContext,
        LoggerFactoryInterface $loggerFactory,
        SaveMultipleOperationsInterface $saveMultipleOperations,
        QueueOperationDataInterfaceFactory $queueOperationDataFactory,
        MessageManagerInterface $messageManager,
        string $bulkDescription = null,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->serializer = $serializer;
        $this->operationRepository = $operationRepository;
        $this->identityService = $identityService;
        $this->bulkManagement = $bulkManagement;
        $this->userContext = $userContext;
        $this->logger = $loggerFactory->create();
        $this->saveMultipleOperations = $saveMultipleOperations;
        $this->queueOperationDataFactory = $queueOperationDataFactory;
        $this->messageManager = $messageManager;
        $this->bulkDescription = $bulkDescription ?? static::DEFAULT_BULK_DESCRIPTION;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws \Exception
     */
    public function publish()
    {
        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = $this->bulkDescription;
        $userId = $this->userContext->getUserId();

        /** create new bulk without operations */
        if (!$this->bulkManagement->scheduleBulk($bulkUuid, [], $bulkDescription, $userId)) {
            throw new LocalizedException(
                __('Something went wrong while scheduling bulk operation request.')
            );
        }

        $operations = [];
        $bulkException = new BulkException();
        foreach ($this->messageManager->getMessages() as $operationId => $topicMessage) {
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
            $this->messageManager->setMessages([]);
        }
    }
}
