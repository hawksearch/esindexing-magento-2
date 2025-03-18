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

namespace HawkSearch\EsIndexing\Plugin;

use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use HawkSearch\EsIndexing\Model\DataIndex;
use HawkSearch\EsIndexing\Model\Indexing;
use HawkSearch\EsIndexing\Model\MessageQueue\Exception\InvalidBulkOperationException;
use HawkSearch\EsIndexing\Model\MessageQueue\Validator\OperationValidatorInterface;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex as DataIndexResource;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\Collection as DataIndexCollection;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\CollectionFactory as DataIndexCollectionFactory;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
use Magento\AsynchronousOperations\Model\OperationProcessor;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\MessageEncoder;
use Magento\Framework\MessageQueue\MessageValidator;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;


class AsynchronousOperationProcessorPlugin
{
    private $loadedIndexCache = [];
    private MessageEncoder $messageEncoder;
    private OperationManagementInterface $operationManagement;
    private OperationValidatorInterface $bulkAllOperationCompleteValidator;
    private OperationValidatorInterface $operationTopicValidator;
    private OperationValidatorInterface $operationOpenStatusValidator;
    private OperationValidatorInterface $operationStage2Validator;
    private HierarchyManagementInterface $hierarchyManagement;
    private IndexManagementInterface $indexManagement;
    private MessageValidator $messageValidator;
    private StoreManagerInterface $storeManager;
    private Indexing\Context $indexingContext;
    private Emulation $emulation;
    private BulkOperationManagement $bulkOperationManagement;
    private DataIndexCollectionFactory $dataIndexCollectionFactory;
    private DataIndexResource $dataIndexResource;

    public function __construct(
        MessageEncoder $messageEncoder,
        OperationManagementInterface $operationManagement,
        OperationValidatorInterface $bulkAllOperationCompleteValidator,
        OperationValidatorInterface $operationTopicValidator,
        OperationValidatorInterface $operationOpenStatusValidator,
        OperationValidatorInterface $operationStage2Validator,
        HierarchyManagementInterface $hierarchyManagement,
        IndexManagementInterface $indexManagement,
        MessageValidator $messageValidator,
        StoreManagerInterface $storeManager,
        Indexing\Context $indexingContext,
        Emulation $emulation,
        BulkOperationManagement $bulkOperationManagement,
        DataIndexCollectionFactory $dataIndexCollectionFactory,
        DataIndexResource $dataIndexResource
    ) {
        $this->messageEncoder = $messageEncoder;
        $this->operationManagement = $operationManagement;
        $this->bulkAllOperationCompleteValidator = $bulkAllOperationCompleteValidator;
        $this->operationTopicValidator = $operationTopicValidator;
        $this->operationOpenStatusValidator = $operationOpenStatusValidator;
        $this->operationStage2Validator = $operationStage2Validator;
        $this->hierarchyManagement = $hierarchyManagement;
        $this->indexManagement = $indexManagement;
        $this->messageValidator = $messageValidator;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->emulation = $emulation;
        $this->bulkOperationManagement = $bulkOperationManagement;
        $this->dataIndexCollectionFactory = $dataIndexCollectionFactory;
        $this->dataIndexResource = $dataIndexResource;
    }

    /**
     * @return ?list<string>
     * @throws LocalizedException
     */
    public function beforeProcess(OperationProcessor $subject, string $encodedMessage): ?array
    {
        /** @todo Mark operation as started */
        /** @var OperationInterface $operation */
        $operation = $this->messageEncoder->decode(AsyncConfig::SYSTEM_TOPIC_NAME, $encodedMessage);
        $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);

        if (!$this->isAllowed($operation)) {
            return null;
        }

        $this->updateOperationStatus($operation);
        // do not change operation status if operation status is not open
        $this->operationOpenStatusValidator->validate($operation);

        return [$this->messageEncoder->encode(AsyncConfig::SYSTEM_TOPIC_NAME, $operation)];
    }

    /**
     * @param OperationProcessor $subject
     * @param null $result
     * @param string $encodedMessage
     * @throws LocalizedException
     * @noinspection PhpMissingParamTypeInspection
     * @phpstan-ignore missingType.parameter
     */
    public function afterProcess(OperationProcessor $subject, $result, string $encodedMessage): void
    {
        /** @var OperationInterface $operation */
        $operation = $this->messageEncoder->decode(AsyncConfig::SYSTEM_TOPIC_NAME, $encodedMessage);
        $this->messageValidator->validate(AsyncConfig::SYSTEM_TOPIC_NAME, $operation);

        if (!$this->isAllowed($operation)) {
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
     * @throws NoSuchEntityException|InvalidBulkOperationException
     */
    private function finalizeFullReindexing(OperationInterface $operation): void
    {
        if (!$this->indexingContext->isFullReindex()) {
            return;
        }

        $this->processStage1OperationCompletion($operation);
        $this->processStage2OperationCompletion($operation);

        if (!$this->validateAllStagesComplete()) {
            return;
        }

        try {
            $this->hierarchyManagement->rebuildHierarchy(
                $this->indexingContext->getIndexName($this->getStoreId())
            );
            $this->indexManagement->switchIndices();
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            $serializedData = $operation->getSerializedData();
            $messages = [
                [__('Error before completing reindexing process')],
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

    private function processStage1OperationCompletion(OperationInterface $operation): void
    {
        if ($this->operationStage2Validator->validate($operation)) {
            return;
        }

        if (!$this->bulkAllOperationCompleteValidator->validate($operation)) {
            return;
        }

        $dataIndex = $this->loadDataIndexByName($this->indexingContext->getIndexName($this->getStoreId()));
        if (!$indexId = $dataIndex->getId()) {
            //@todo throw an exception here just not to make it silent
            return;
        }

        $dataIndex->setIsStage1Complete(true);
        $this->dataIndexResource->save($dataIndex);
    }

    private function processStage2OperationCompletion(OperationInterface $operation): void
    {
        if (!$this->operationStage2Validator->validate($operation)) {
            return;
        }

        $dataIndex = $this->loadDataIndexByName($this->indexingContext->getIndexName($this->getStoreId()));
        if (!$indexId = $dataIndex->getId()) {
            //@todo throw an exception here just not to make it silent
            return;
        }

        $dataIndex->setStage2Completed($dataIndex->getStage2Completed() + 1);
        $this->dataIndexResource->save($dataIndex);
    }

    private function validateAllStagesComplete(): bool
    {
        $dataIndex = $this->loadDataIndexByName($this->indexingContext->getIndexName($this->getStoreId()));

        return $dataIndex->getIsStage1Complete()
            && $dataIndex->getStage2Scheduled() > 0
            && $dataIndex->getStage2Scheduled() == $dataIndex->getStage2Completed();
    }

    private function isAllowed(OperationInterface $operation): bool
    {
        $isAllowed = true;
        try {
            $this->operationTopicValidator->validate($operation);
        } catch (InvalidBulkOperationException $e) {
            $isAllowed = false;
        }

        return $isAllowed;
    }

    /**
     * Update operation status from magento_operation table
     */
    private function updateOperationStatus(OperationInterface $operation): void
    {
        try {
            $loadedOperation = $this->bulkOperationManagement->getOperationByBulkAndKey(
                $operation->getBulkUuid(),
                $operation->getId()
            );
            $operation->setStatus($loadedOperation->getStatus());
        } catch (NoSuchEntityException $e) {
            //operation is not created in the bulk yet
        }
    }

    private function loadDataIndexByName(string $indexName): DataIndex
    {
        if (!isset($this->loadedIndexCache[$indexName][$this->getStoreId()])) {
            /** @var DataIndexCollection $collection */
            $collection = $this->dataIndexCollectionFactory->create()
                ->addFieldToFilter('engine_index_name', $indexName)
                ->addFieldToFilter('store_id', $this->getStoreId());

            $this->loadedIndexCache[$indexName][$this->getStoreId()] = $collection->getFirstItem();
        }

        /** @var DataIndex */
        return $this->loadedIndexCache[$indexName][$this->getStoreId()];
    }

    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
