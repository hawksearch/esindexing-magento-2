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
namespace HawkSearch\EsIndexing\Model\MessageQueue;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Publisher implements PublisherInterface
{
    public const TOPIC_NAME = 'hawksearch.indexing';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var OperationInterfaceFactory
     */
    private $operationFactory;

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
     * Publisher constructor.
     * @param SerializerInterface $serializer
     * @param OperationInterfaceFactory $operationFactory
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        SerializerInterface $serializer,
        OperationInterfaceFactory $operationFactory,
        IdentityGeneratorInterface $identityService,
        BulkManagementInterface $bulkManagement,
        UserContextInterface $userContext,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->operationFactory = $operationFactory;
        $this->identityService = $identityService;
        $this->bulkManagement = $bulkManagement;
        $this->userContext = $userContext;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param array $data List of data to be published
     * @param string $topicName Bulk operation description
     * @inheritDoc
     * @throws LocalizedException
     */
    public function publish($topicName, $data)
    {
        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = $topicName;
        $operations = [];

        foreach ($data as $dataToUpdate) {
            $this->updateApplicationHeaders($dataToUpdate);
            $operations[] = $this->makeOperation(
                $bulkUuid,
                static::TOPIC_NAME,
                $dataToUpdate
            );
        }

        if (!empty($operations)) {
            $result = $this->bulkManagement->scheduleBulk(
                $bulkUuid,
                $operations,
                $bulkDescription,
                $this->userContext->getUserId()
            );
            if (!$result) {
                throw new LocalizedException(
                    __('Something went wrong while processing the request.')
                );
            }
        }
    }

    private function makeOperation($bulkUuid, $queue, $dataToEncode): OperationInterface
    {
        $data = [
            'data' => [
                'bulk_uuid' => $bulkUuid,
                'topic_name' => $queue,
                'serialized_data' => $this->serializer->serialize($dataToEncode),
                'status' => \Magento\Framework\Bulk\OperationInterface::STATUS_TYPE_OPEN,
            ]
        ];

        return $this->operationFactory->create($data);
    }

    /**
     * Set current store_id in messageData['application_headers']
     * so consumer may check store_id and execute operation in correct store scope.
     * Prevent publishing inconsistent messages because of store_id not defined or wrong.
     * @param array $data
     */
    private function updateApplicationHeaders(array &$data)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $errorMessage = sprintf(
                "Can't get current storeId and inject to the message queue. Error %s.",
                $e->getMessage()
            );
            $this->logger->error($errorMessage);
            throw new \LogicException($errorMessage);
        }

        if (!isset($data['application_headers'])) {
            $data['application_headers'] = [];
        }
        $data['application_headers']['store_id'] = $storeId;
    }
}
