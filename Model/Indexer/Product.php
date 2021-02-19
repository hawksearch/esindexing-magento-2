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
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\MessageQueue\BulkPublisherInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractIndexer implements IndexerActionInterface, MviewActionInterface
{
    public const ENTITY_INDEXER_CODE = 'product';

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
     * @var BulkPublisherInterface
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
     * @param BulkPublisherInterface $publisher
     * @param SerializerInterface $serializer
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param UserContextInterface $userContext
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        StoreManagerInterface $storeManager,
        General $generalConfig,
        Indexing $indexingConfig,
        BulkPublisherInterface $publisher,
        SerializerInterface $serializer,
        IdentityGeneratorInterface $identityService,
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
            $this->rebuildPartial($ids);
        } else {
            $this->rebuildFull();
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

            $batchSize = $this->indexingConfig->getItemsBatchSize($store->getId());
            $productIdsChunks = array_chunk($productIds, $batchSize);

            foreach ($productIdsChunks as $productIdsChunk) {
                $dataToUpdate = [
                    'class' => ProductEntityIndexer::class,
                    'method' => 'rebuildEntityIndex',
                    'method_arguments' => [],
                    'store_id' => $store->getId(),
                    'ids' => $productIdsChunk,
                    'full_reindex' => false,
                    'size' => count($productIdsChunk)
                ];
                /*$operations[] = $this->makeOperation(
                    $bulkUuid,
                    self::TOPIC_NAME,
                    $dataToUpdate
                );*/
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

    /**
     * @return string
     */
    protected function getEntityIndexerCode()
    {
        return self::ENTITY_INDEXER_CODE;
    }
}
