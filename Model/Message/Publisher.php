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
namespace HawkSearch\EsIndexing\Model\Message;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\BulkPublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Publisher implements BulkPublisherInterface
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
     * Publisher constructor.
     * @param SerializerInterface $serializer
     * @param OperationInterfaceFactory $operationFactory
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     */
    public function __construct(
        SerializerInterface $serializer,
        OperationInterfaceFactory $operationFactory,
        IdentityGeneratorInterface $identityService,
        BulkManagementInterface $bulkManagement,
        UserContextInterface $userContext
    ) {
        $this->serializer = $serializer;
        $this->operationFactory = $operationFactory;
        $this->identityService = $identityService;
        $this->bulkManagement = $bulkManagement;
        $this->userContext = $userContext;
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
}
