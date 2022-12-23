<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractItemsIndexer
{
    /**
     * @var BulkPublisherInterface
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
     * @var EntityTypePoolInterface
     */
    private $entityTypePool;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var MessageTopicResolverInterface
     */
    private $messageTopicResolver;

    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * AbstractIndexer constructor.
     * @param BulkPublisherInterface $publisher
     * @param StoreManagerInterface $storeManager
     * @param Indexing $indexingConfig
     * @param EntityTypePoolInterface $entityTypePool
     * @param EventManagerInterface $eventManager
     * @param MessageTopicResolverInterface $messageTopicResolver
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        BulkPublisherInterface $publisher,
        StoreManagerInterface $storeManager,
        Indexing $indexingConfig,
        EntityTypePoolInterface $entityTypePool,
        EventManagerInterface $eventManager,
        MessageTopicResolverInterface $messageTopicResolver,
        IndexManagementInterface $indexManagement
    ) {
        $this->publisher = $publisher;
        $this->storeManager = $storeManager;
        $this->indexingConfig = $indexingConfig;
        $this->entityTypePool = $entityTypePool;
        $this->eventManager = $eventManager;
        $this->messageTopicResolver = $messageTopicResolver;
        $this->indexManagement = $indexManagement;
    }

    /**
     * Rebuild delta items index
     * @param array $ids
     * @throws NoSuchEntityException
     * @throws InputException
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
                    'topic' => $this->messageTopicResolver->resolve($this->getEntityType()),
                    'class' => get_class($this->getEntityType()->getEntityIndexer()),
                    'method' => 'rebuildEntityIndex',
                    'method_arguments' => [
                        'entityIds' => $chunk
                    ],
                    'full_reindex' => false,
                ];
            }

            $this->publishData($dataToUpdate);
        }

        $this->storeManager->setCurrentStore($currentStore);
    }

    /**
     * Rebuild full items index
     * @throws NoSuchEntityException
     * @throws InputException
     */
    protected function rebuildFull()
    {
        $stores = $this->storeManager->getStores();
        $currentStore = $this->storeManager->getStore();

        foreach ($stores as $store) {
            if (!$this->indexingConfig->isIndexingEnabled($store->getId())) {
                continue;
            }

            $this->storeManager->setCurrentStore($store->getId());

            $this->indexManagement->initializeFullReindex();

            $dataToUpdate = [];
            $dataToUpdate[] = [
                'topic' => 'hawksearch.indexing.fullreindex.start',
                'full_reindex' => true,
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

            foreach ($this->entityTypePool->getList() as $entityType) {
                $items = $entityType->getItemsProvider()->getItems($store->getId());
                $batches = ceil(count($items) / $batchSize);

                $transport = new DataObject($dataToUpdate);
                $this->eventManager->dispatch(
                    'hawksearch_esindexing_rebuild_full_index_items_before',
                    [
                        'store' => $store,
                        'indexer' => $this,
                        'entity_type' => $entityType,
                        'items' => $items,
                        'transport' => $transport
                    ]
                );
                $dataToUpdate = $transport->getData();

                for ($page = 1; $page <= $batches; $page++) {
                    $dataToUpdate[] = [
                        'topic' => $this->messageTopicResolver->resolve($entityType),
                        'class' => get_class($entityType->getEntityIndexer()),
                        'method' => 'rebuildEntityIndexBatch',
                        'method_arguments' => [
                            'currentPage' => $page,
                            'pageSize' => $batchSize
                        ],
                        'full_reindex' => true,
                    ];
                }

                $transport = new DataObject($dataToUpdate);
                $this->eventManager->dispatch(
                    'hawksearch_esindexing_rebuild_full_index_items_after',
                    [
                        'store' => $store,
                        'indexer' => $this,
                        'entity_type' => $entityType,
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

            $this->publishData($dataToUpdate);
        }

        $this->storeManager->setCurrentStore($currentStore);
    }

    /**
     * @return EntityTypeInterface
     * @throws NotFoundException
     */
    protected function getEntityType()
    {
        return $this->entityTypePool->get($this->getEntityTypeName());
    }

    /**
     * @param array $dataToUpdate
     */
    private function publishData(array $dataToUpdate): void
    {
        foreach ($dataToUpdate as $data) {
            $topic = $data['topic'] ?? '';
            unset($data['topic']);
            $this->publisher->addMessage($topic, $data);
        }
        $this->publisher->publish();
    }

    /**
     * @return string
     */
    abstract protected function getEntityTypeName();
}
