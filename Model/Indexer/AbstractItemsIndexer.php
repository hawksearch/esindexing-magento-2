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

namespace HawkSearch\EsIndexing\Model\Indexer;

use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\EntityIndexerPoolInterface;
use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsProviderPoolInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\PublisherInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractItemsIndexer
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Indexing
     */
    private $indexingConfig;

    /**
     * @var EntityIndexerPoolInterface
     */
    private $entityIndexerPool;

    /**
     * @var ItemsProviderPoolInterface
     */
    private $itemsProviderPool;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * AbstractIndexer constructor.
     * @param PublisherInterface $publisher
     * @param StoreManagerInterface $storeManager
     * @param Indexing $indexingConfig
     * @param EntityIndexerPoolInterface $entityIndexerPool
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        PublisherInterface $publisher,
        StoreManagerInterface $storeManager,
        Indexing $indexingConfig,
        EntityIndexerPoolInterface $entityIndexerPool,
        ItemsProviderPoolInterface $itemsProviderPool,
        EventManagerInterface $eventManager
    ) {
        $this->publisher = $publisher;
        $this->storeManager = $storeManager;
        $this->indexingConfig = $indexingConfig;
        $this->entityIndexerPool = $entityIndexerPool;
        $this->itemsProviderPool = $itemsProviderPool;
        $this->eventManager = $eventManager;
    }

    /**
     * Rebuild delta items index
     * @param array $ids
     * @throws NoSuchEntityException
     */
    protected function rebuildDelta($ids)
    {
        $stores = $this->storeManager->getStores();

        $currentStore = $this->storeManager->getStore();
        foreach ($stores as $store) {
            if (!$this->indexingConfig->isIndexingEnabled($store->getId())) {
                continue;
            }

            $this->storeManager->setCurrentStore($store->getId());

            $batchSize = $this->indexingConfig->getItemsBatchSize($store->getId());
            $chunks = array_chunk($ids, $batchSize);

            $dataToUpdate = [];
            foreach ($chunks as $chunk) {
                $dataToUpdate[] = [
                    'class' => get_class($this->entityIndexerPool->getIndexerByCode($this->getEntityIndexerCode())),
                    'method' => 'rebuildEntityIndex',
                    'method_arguments' => [
                        'storeId' => $store->getId(),
                        'entityIds' => $chunk
                    ],
                    'full_reindex' => false,
                ];
            }

            $this->publisher->publish(
                __(
                    'Update delta index for %1 items of %2 entity',
                    count($ids),
                    $this->entityIndexerPool->getIndexerByCode($this->getEntityIndexerCode())
                ),
                $dataToUpdate
            );
        }
        $this->storeManager->setCurrentStore($currentStore);
    }

    /**
     * Rebuild full items index
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    protected function rebuildFull()
    {
        $stores = $this->storeManager->getStores();
        $currentStore = $this->storeManager->getStore();

        foreach ($stores as $store) {
            if (!$this->indexingConfig->isIndexingEnabled($store->getId())) {
                continue;
            }
            $dataToUpdate = [];

            $this->storeManager->setCurrentStore($store->getId());

            $dataToUpdate[] = [
                'class' => IndexManagementInterface::class,
                'method' => 'initializeFullReindex'
            ];

            $transport = new DataObject($dataToUpdate);
            $this->eventManager->dispatch(
                'hawksearch_esindexing_indexers_rebuild_full_before',
                [
                    'store' => $store,
                    'indexer' => $this,
                    'transport' => $transport
                ]
            );
            $dataToUpdate = $transport->getData();

            $batchSize = $this->indexingConfig->getItemsBatchSize($store->getId());

            foreach ($this->entityIndexerPool->getIndexerList() as $indexerCode => $entityIndexer) {
                $items = $this->itemsProviderPool->get($indexerCode)->getItems($store->getId());
                $batches = ceil(count($items) / $batchSize);

                $transport = new DataObject($dataToUpdate);
                $this->eventManager->dispatch(
                    'hawksearch_esindexing_rebuild_full_index_items_before',
                    [
                        'store' => $store,
                        'indexer' => $this,
                        'items_indexer_code' => $indexerCode,
                        'items' => $items,
                        'transport' => $transport
                    ]
                );
                $dataToUpdate = $transport->getData();

                for ($page = 1; $page <= $batches; $page++) {
                    $dataToUpdate[] = [
                        'class' => get_class($this->entityIndexerPool->getIndexerByCode($indexerCode)),
                        'method' => 'rebuildEntityIndexBatch',
                        'method_arguments' => [
                            'storeId' => $store->getId(),
                            'currentPage' => $page,
                            'pageSize' => $batchSize
                        ],
                        'full_reindex' => false,
                    ];
                }

                $transport = new DataObject($dataToUpdate);
                $this->eventManager->dispatch(
                    'hawksearch_esindexing_rebuild_full_index_items_after',
                    [
                        'store' => $store,
                        'indexer' => $this,
                        'items_indexer_code' => $indexerCode,
                        'items' => $items,
                        'transport' => $transport
                    ]
                );
                $dataToUpdate = $transport->getData();
            }

            $transport = new DataObject($dataToUpdate);
            $this->eventManager->dispatch(
                'hawksearch_esindexing_indexers_rebuild_full_after',
                [
                    'store' => $store,
                    'indexer' => $this,
                    'transport' => $transport
                ]
            );
            $dataToUpdate = $transport->getData();

            $dataToUpdate[] = [
                'class' => IndexManagementInterface::class,
                'method' => 'switchIndices'
            ];

            $this->publisher->publish(
                __('Update full items index for store "%1"', $store->getCode()),
                $dataToUpdate
            );
        }
        $this->storeManager->setCurrentStore($currentStore);
    }

    /**
     * @return string
     */
    abstract public function getEntityIndexerCode();
}
