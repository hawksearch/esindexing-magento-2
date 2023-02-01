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

namespace HawkSearch\EsIndexing\Plugin;

use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing;
use HawkSearch\EsIndexing\Model\MessageQueue\IndexingOperationValidator;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
use Magento\AsynchronousOperations\Model\OperationProcessor;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\Exception\BulkException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\MessageEncoder;
use Magento\Framework\MessageQueue\MessageValidator;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class AsynchronousOperationProcessorPlugin
{
    /**
     * @var MessageEncoder
     */
    private $messageEncoder;

    /**
     * @var OperationManagementInterface
     */
    private $operationManagement;

    /**
     * @var IndexingOperationValidator
     */
    private $indexingOperationValidator;

    /**
     * @var HierarchyManagementInterface
     */
    private HierarchyManagementInterface $hierarchyManagement;

    /**
     * @var IndexManagementInterface
     */
    private IndexManagementInterface $indexManagement;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var MessageValidator
     */
    private MessageValidator $messageValidator;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Indexing\Context
     */
    private Indexing\Context $indexingContext;

    /**
     * @var Emulation
     */
    private Emulation $emulation;

    /**
     * AsynchronousOperationProcessorPlugin constructor.
     *
     * @param MessageEncoder $messageEncoder
     * @param OperationManagementInterface $operationManagement
     * @param IndexingOperationValidator $indexingOperationValidator
     * @param HierarchyManagementInterface $hierarchyManagement
     * @param IndexManagementInterface $indexManagement
     * @param SerializerInterface $serializer
     * @param MessageValidator $messageValidator
     * @param StoreManagerInterface $storeManager
     * @param Indexing\Context $indexingContext
     */
    public function __construct(
        MessageEncoder $messageEncoder,
        OperationManagementInterface $operationManagement,
        IndexingOperationValidator $indexingOperationValidator,
        HierarchyManagementInterface $hierarchyManagement,
        IndexManagementInterface $indexManagement,
        SerializerInterface $serializer,
        MessageValidator $messageValidator,
        StoreManagerInterface $storeManager,
        Indexing\Context $indexingContext,
        Emulation $emulation
    )
    {
        $this->messageEncoder = $messageEncoder;
        $this->operationManagement = $operationManagement;
        $this->indexingOperationValidator = $indexingOperationValidator;
        $this->hierarchyManagement = $hierarchyManagement;
        $this->indexManagement = $indexManagement;
        $this->serializer = $serializer;
        $this->messageValidator = $messageValidator;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->emulation = $emulation;
    }

    /**
     * @param OperationProcessor $subject
     * @param string $encodedMessage
     * @return array
     * @throws LocalizedException
     */
    public function beforeProcess(OperationProcessor $subject, string $encodedMessage)
    {
        /** @todo Mark operation as started */
        /** @var OperationInterface $operation */
        $operation = $this->messageEncoder->decode(AsyncConfig::SYSTEM_TOPIC_NAME, $encodedMessage);
        $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);

        if (!$this->indexingOperationValidator->isOperationTopicAllowed($operation)) {
            return null;
        }

        try {
            if (!$this->indexingOperationValidator->isValidOperation($operation)) {
                throw new BulkException(
                    __(
                        'Can\'t process operation Bulk UUID: %1, key: %2',
                        $operation->getBulkUuid(),
                        $operation->getId()
                    )
                );
            }
        } catch (BulkException | NoSuchEntityException $e) {
            //@TODO Add error code mapping
            $errorCode = 100;
            $serializedData = (isset($errorCode)) ? $operation->getSerializedData() : null;
            $this->operationManagement->changeOperationStatus(
                $operation->getBulkUuid(),
                $operation->getId(),
                OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                $errorCode,
                implode('; ', array_merge([], ...[[$e->getMessage()]])),
                $serializedData
            );

            //re-throw exception
            throw $e;
        }

        return null;
    }

    /**
     * @param OperationProcessor $subject
     * @param $result
     * @param string $encodedMessage
     * @return void
     * @throws LocalizedException
     */
    public function afterProcess(OperationProcessor $subject, $result, string $encodedMessage)
    {
        /** @var OperationInterface $operation */
        $operation = $this->messageEncoder->decode(AsyncConfig::SYSTEM_TOPIC_NAME, $encodedMessage);
        $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);

        if (!$this->indexingOperationValidator->isOperationTopicAllowed($operation)) {
            return;
        }

        try {
            $this->finalizeFullReindexing($operation);
        } catch (\Exception $e) {
            $this->emulation->stopEnvironmentEmulation();
            throw $e;
        }

        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * Finalize full reindexing process: rebuild hierarchy, set current index
     *
     * @param OperationInterface $operation
     * @return void
     * @throws NoSuchEntityException
     */
    private function finalizeFullReindexing(OperationInterface $operation)
    {
        if (!$this->indexingContext->isFullReindex()) {
            return;
        }

        if (!$this->indexingOperationValidator->isAllBulkOperationsComplete($operation)) {
            return;
        }

        try {
            $this->hierarchyManagement->rebuildHierarchy(
                $this->indexingContext->getIndexName((int)$this->storeManager->getStore()->getId())
            );
            $this->indexManagement->switchIndices();
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            $serializedData = $operation->getSerializedData();
            $messages = [
                [__('Error before compliting reindexing process')],
                [$e->getMessage()],
            ];
            $this->operationManagement->changeOperationStatus(
                $operation->getBulkUuid(),
                $operation->getId(),
                OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                $errorCode,
                implode('; ', array_merge([], ...$messages)),
                $serializedData
            );

            //re-throw exception
            throw $e;
        }
    }
}
