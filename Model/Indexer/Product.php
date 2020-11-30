<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexer;

use HawkSearch\EsIndexing\Model\Config\General;
use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\ProductEntityIndexer;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Product implements IndexerActionInterface, MviewActionInterface
{
    public const TOPIC_NAME = 'hawksearch.indexing';

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var General
     */
    private $generalConfig;

    /**
     * @var Indexing
     */
    private $indexingConfig;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
     * @var OperationInterfaceFactory
     */
    private $operationFactory;

    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * Product constructor.
     * @param ProductDataProvider $productDataProvider
     * @param StoreManagerInterface $storeManager
     * @param General $generalConfig
     * @param Indexing $indexingConfig
     * @param PublisherInterface $publisher
     * @param SerializerInterface $serializer
     * @param IdentityGeneratorInterface $identityService
     * @param OperationInterfaceFactory $operartionFactory
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        StoreManagerInterface $storeManager,
        General $generalConfig,
        Indexing $indexingConfig,
        PublisherInterface $publisher,
        SerializerInterface $serializer,
        IdentityGeneratorInterface $identityService,
        OperationInterfaceFactory $operartionFactory,
        BulkManagementInterface $bulkManagement,
        UserContextInterface $userContext
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->storeManager = $storeManager;
        $this->generalConfig = $generalConfig;
        $this->indexingConfig = $indexingConfig;
        $this->publisher = $publisher;
        $this->serializer = $serializer;
        $this->identityService = $identityService;
        $this->operartionFactory = $operartionFactory;
        $this->bulkManagement = $bulkManagement;
        $this->userContext = $userContext;
    }

    /**
     * @inheritDoc
     */
    public function executeFull()
    {
        $this->execute(null);
    }

    /**
     * @inheritDoc
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @inheritDoc
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @inheritDoc
     */
    public function execute($ids)
    {
        if ($ids) {
            $ids = array_merge($ids, $this->productDataProvider->getParentProductIds($ids));
        }

        if (is_array($ids) && count($ids) > 0) {
            $this->publishPartialReindex($ids);
        } else {
            $this->publishFullReindex();
        }
    }

    /**
     * @param array $productIds
     * @param StoreInterface $store
     * @throws LocalizedException
     */
    private function publishPartialReindex($productIds)
    {
        $stores = $this->storeManager->getStores();
        $operations = [];

        foreach ($stores as $store) {
            if (!$this->generalConfig->isIndexingEnabled($store->getId())) {
                continue;
            }

            $bulkSize = $this->indexingConfig->getItemsBatchSize($store->getId());
            $productIdsChunks = array_chunk($productIds, $bulkSize);

            foreach ($productIdsChunks as $productIdsChunk) {
                $dataToUpdate = [
                    'class' => ProductEntityIndexer::class,
                    'method' => 'rebuildEntityIndex',
                    'store_id' => $store->getId(),
                    'ids' => $productIdsChunk,
                    'full_reindex' => false,
                    'size' => count($productIdsChunk)
                ];
                $operations[] = $this->makeOperation(
                    $bulkUuid,
                    self::TOPIC_NAME,
                    $dataToUpdate
                );
            }
        }

        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = __('Update delta index for ' . count($productIds) . ' selected products');
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

    private function publishFullReindex()
    {
        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = __('Full reindex');
        $operations = [];
        //publish message to queue
        /**
         * @uses \HawkSearch\EsIndexing\Model\Indexing\ProductEntityIndexer::rebuildEntityIndex
         */
    }

    private function publish($productIds, $store, $isFullReindex)
    {
        $bulkSize = $this->indexingConfig->getItemsBatchSize($store->getId());
        $productIdsChunks = array_chunk($productIds, $bulkSize);
        $bulkUuid = $this->identityService->generateId();
        $bulkDescription = __('Reindex ' . count($productIds) . ' selected products');
        $operations = [];

        /*$this->queue->addToQueue(
            Data::class,
            'rebuildStoreProductIndex',
            ['store_id' => $storeId, 'product_ids' => $chunk],
            count($chunk)
        );*/

        foreach ($productIdsChunks as $productIdsChunk) {
                $dataToUpdate = [
                    'class' => ProductEntityIndexer::class,
                    'method' => 'rebuildEntityIndex',
                    'store_id' => $store->getId(),
                    'ids' => $productIdsChunk,
                    'full_reindex',
                    'size'
                ];
                $operations[] = $this->makeOperation(
                    $bulkUuid,
                    self::TOPIC_NAME,

                );

            if ($attributesData) {
                $operations[] = $this->makeOperation(
                    'Update product attributes',
                    'product_action_attribute.update',
                    $attributesData,
                    $storeId,
                    $websiteId,
                    $productIdsChunk,
                    $bulkUuid
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
