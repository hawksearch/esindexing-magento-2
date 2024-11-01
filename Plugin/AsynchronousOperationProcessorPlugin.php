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
use HawkSearch\EsIndexing\Model\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\Context;
use HawkSearch\EsIndexing\Model\MessageQueue\Exception\InvalidBulkOperationException;
use HawkSearch\EsIndexing\Model\MessageQueue\Validator\OperationValidatorInterface;
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
    /**
     * @var MessageEncoder
     */
    private MessageEncoder $messageEncoder;

    /**
     * @var OperationManagementInterface
     */
    private OperationManagementInterface $operationManagement;

    /**
     * @var OperationValidatorInterface
     */
    private OperationValidatorInterface $bulkAllOperationCompleteValidator;

    /**
     * @var OperationValidatorInterface
     */
    private OperationValidatorInterface $operationTopicValidator;
    /**
     * @var OperationValidatorInterface
     */
    private OperationValidatorInterface $operationOpenStatusValidator;

    /**
     * @var HierarchyManagementInterface
     */
    private HierarchyManagementInterface $hierarchyManagement;

    /**
     * @var IndexManagementInterface
     */
    private IndexManagementInterface $indexManagement;

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
     * @var BulkOperationManagement
     */
    private BulkOperationManagement $bulkOperationManagement;

    /**
     * AsynchronousOperationProcessorPlugin constructor.
     *
     * @param MessageEncoder $messageEncoder
     * @param OperationManagementInterface $operationManagement
     * @param OperationValidatorInterface $bulkAllOperationCompleteValidator
     * @param OperationValidatorInterface $operationTopicValidator
     * @param OperationValidatorInterface $operationOpenStatusValidator
     * @param HierarchyManagementInterface $hierarchyManagement
     * @param IndexManagementInterface $indexManagement
     * @param MessageValidator $messageValidator
     * @param StoreManagerInterface $storeManager
     * @param Context $indexingContext
     * @param Emulation $emulation
     * @param BulkOperationManagement $bulkOperationManagement
     */
    public function __construct(
        MessageEncoder $messageEncoder,
        OperationManagementInterface $operationManagement,
        OperationValidatorInterface $bulkAllOperationCompleteValidator,
        OperationValidatorInterface $operationTopicValidator,
        OperationValidatorInterface $operationOpenStatusValidator,
        HierarchyManagementInterface $hierarchyManagement,
        IndexManagementInterface $indexManagement,
        MessageValidator $messageValidator,
        StoreManagerInterface $storeManager,
        Indexing\Context $indexingContext,
        Emulation $emulation,
        BulkOperationManagement $bulkOperationManagement
    )
    {
        $this->messageEncoder = $messageEncoder;
        $this->operationManagement = $operationManagement;
        $this->bulkAllOperationCompleteValidator = $bulkAllOperationCompleteValidator;
        $this->operationTopicValidator = $operationTopicValidator;
        $this->operationOpenStatusValidator = $operationOpenStatusValidator;
        $this->hierarchyManagement = $hierarchyManagement;
        $this->indexManagement = $indexManagement;
        $this->messageValidator = $messageValidator;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->emulation = $emulation;
        $this->bulkOperationManagement = $bulkOperationManagement;
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
     * @return void
     * @throws LocalizedException
     * @noinspection PhpMissingParamTypeInspection
     * @phpstan-ignore missingType.parameter
     */
    public function afterProcess(OperationProcessor $subject, $result, string $encodedMessage)
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
     * @param OperationInterface $operation
     * @return void
     * @throws NoSuchEntityException|InvalidBulkOperationException
     */
    private function finalizeFullReindexing(OperationInterface $operation)
    {
        if (!$this->indexingContext->isFullReindex()) {
            return;
        }

        if (!$this->bulkAllOperationCompleteValidator->validate($operation)) {
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

    /**
     * @param OperationInterface $operation
     * @return bool
     */
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
     *
     * @param OperationInterface $operation
     * @return void
     */
    private function updateOperationStatus(OperationInterface $operation)
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
}
