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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Indexing;

use Exception;
use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

abstract class AbstractEntityIndexer implements EntityIndexerInterface
{
    /**
     * @var array
     */
    private $itemsToRemoveCache = [];

    /**
     * @var array
     */
    private $itemsToIndexCache = [];

    /**
     * @var IndexingConfig
     */
    private $indexingConfig;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var ItemsProviderPoolInterface
     */
    private $itemsProviderPool;

    /**
     * @var EntityIndexerPoolInterface
     */
    private $entityIndexerPool;

    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $hawkLogger;

    /**
     * AbstractEntityIndexer constructor.
     * @param IndexingConfig $indexingConfig
     * @param Emulation $emulation
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param EntityIndexerPoolInterface $entityIndexerPool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     */
    public function __construct(
        IndexingConfig $indexingConfig,
        Emulation $emulation,
        ItemsProviderPoolInterface $itemsProviderPool,
        EntityIndexerPoolInterface $entityIndexerPool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory
    )
    {
        $this->indexingConfig = $indexingConfig;
        $this->emulation = $emulation;
        $this->itemsProviderPool = $itemsProviderPool;
        $this->entityIndexerPool = $entityIndexerPool;
        $this->indexManagement = $indexManagement;
        $this->eventManager = $eventManager;
        $this->hawkLogger = $loggerFactory->create();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function rebuildEntityIndex(int $storeId, $entityIds = null)
    {
        if (!$this->indexingConfig->isIndexingEnabled($storeId)) {
            return;
        }

        $batchSize = $this->indexingConfig->getItemsBatchSize($storeId);
        $batches = ceil(count($entityIds) / $batchSize);

        for ($page = 1; $page <= $batches; $page++) {
            $this->rebuildEntityIndexBatch($storeId, $page, $batchSize, $entityIds);
        }
    }

    /**
     * @inheritDoc
     */
    public function rebuildEntityIndexBatch(int $storeId, int $currentPage, int $pageSize, ?array $entityIds = null)
    {
        if (!$this->indexingConfig->isIndexingEnabled($storeId)) {
            return;
        }

        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        $this->hawkLogger->debug(
            sprintf(
                "Starting indexing for Entity type %s, Store %d, Page %d, Page size %d, IDs %s",
                $this->getEntityType(),
                $storeId,
                $currentPage,
                $pageSize,
                implode(',', $entityIds ?? [])
            )
        );

        try {
            $items = $this->itemsProviderPool->get($this->getEntityType())
                ->getItems($storeId, $entityIds, $currentPage, $pageSize);

            $this->hawkLogger->debug(
                sprintf(
                    "Collected %d items",
                    count($items)
                )
            );

            $isFullReindex = $entityIds === null;
            $isCurrentIndex = !$isFullReindex;

            $indexName = $this->indexManagement->getIndexName($isCurrentIndex);

            $this->hawkLogger->debug(
                sprintf(
                    "Picked index: %s",
                    $indexName
                )
            );

            $itemsToRemove = $this->getItemsToRemove($items, $entityIds);
            $this->hawkLogger->debug(
                sprintf(
                    "Items to be removed from the index: %s",
                    implode(',', $itemsToRemove)
                )
            );
            $this->deleteItemsFromIndex($itemsToRemove, $indexName);

            $itemsToIndex = $this->getItemsToIndex($items, $entityIds);
            $this->hawkLogger->debug(
                sprintf(
                    "Items to be indexed: %s",
                    implode(',', array_keys($itemsToIndex))
                )
            );

            $this->deleteItemsFromIndex($itemsToRemove, $indexName);
            $this->indexItems($itemsToIndex, $indexName);


        } catch (Exception $e) {
            $this->emulation->stopEnvironmentEmulation();
            throw $e;
        }
        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @param DataObject[] $fullItemsList Full list of items to be indexed
     * @param array|null $entityIds List of entity IDs used for items selection
     * @return array
     * @throws NotFoundException
     */
    protected function getItemsToRemove(array $fullItemsList, ?array $entityIds = null): array
    {
        $idsToRemove = is_array($entityIds) ? array_combine($entityIds, $entityIds) : [];
        $itemsToRemove = [];

        foreach ($fullItemsList as $item) {
            // Don't remove item from the index if it is in the list of indexed items
            if (isset($idsToRemove[$this->getEntityId($item)])) {
                unset($idsToRemove[$this->getEntityId($item)]);
            }

            if (isset($itemsToRemove[$this->getEntityId($item)])
                || isset($this->itemsToRemoveCache[$this->getEntityId($item)])
                || isset($this->itemsToIndexCache[$this->getEntityId($item)])
            ) {
                continue;
            }

            if (!$this->canItemBeIndexed($item)) {
                $itemsToRemove[$this->getEntityId($item)] = $this->getEntityUniqueId($item);
                $this->itemsToRemoveCache[$this->getEntityId($item)] = $this->getEntityUniqueId($item);
            }
        }

        $itemsToRemove = array_merge($itemsToRemove, $idsToRemove);

        return $itemsToRemove;
    }

    /**
     * @param array $fullItemsList Full list of items to be indexed
     * @param array|null $entityIds List of entity IDs used for items selection
     * @return array
     * @throws LocalizedException
     */
    protected function getItemsToIndex(array $fullItemsList, ?array $entityIds = null): array
    {
        $itemsToIndex = [];

        foreach ($fullItemsList as $item) {
            if (isset($itemsToIndex[$this->getEntityId($item)])
                || isset($this->itemsToIndexCache[$this->getEntityId($item)])
                || isset($this->itemsToRemoveCache[$this->getEntityId($item)])
            ) {
                continue;
            }

            if ($this->canItemBeIndexed($item)) {
                $itemsToIndex[$this->getEntityId($item)] = $this->convertEntityToIndexDataArray($item);
                $this->itemsToIndexCache[$this->getEntityId($item)] = $this->getEntityUniqueId($item);
            }
        }

        return $itemsToIndex;
    }

    /**
     * @param DataObject $item
     * @return array
     * @throws LocalizedException
     */
    protected function convertEntityToIndexDataArray(DataObject $item): array
    {
        $itemData = [];
        $itemData[$this->getEntityIdField()] = $this->getEntityUniqueId($item);
        $itemData[$this->getEntityTypeField()] = $this->getEntityType();
        foreach ($this->getIndexedAttributes() as $attribute) {
            if (!$attribute) {
                continue;
            }
            $itemData[$attribute] = $this->getAttributeValue($item, $attribute);
        }

        $transport = new DataObject($itemData);
        $this->eventManager->dispatch(
            'hawksearch_esindexing_convert_entity_item_after',
            ['item' => $item, 'item_data' => $transport]
        );
        $itemData = $transport->getData();

        $itemDataResult = [];
        foreach ($itemData as $dataKey => $value) {
            $value = $this->castAttributeValue($value);
            if ($value === null) {
                continue;
            }
            $itemDataResult[$dataKey] = $value;
        }

        return $itemDataResult;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function castAttributeValue($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value !== null && !is_array($value) ? array($value) : $value;
    }

    /**
     * @return array
     */
    protected function getIndexedAttributes(): array
    {
        return [];
    }

    /**
     * @param DataObject $item
     * @param string $attribute
     * @return mixed
     */
    abstract protected function getAttributeValue(DataObject $item, string $attribute);

    /**
     * @param DataObject $item
     * @return bool
     */
    abstract protected function canItemBeIndexed(DataObject $item): bool;

    /**
     * @return string
     * @throws NotFoundException
     */
    protected function getEntityType(): string
    {
        foreach ($this->entityIndexerPool->getIndexerList() as $code => $indexer) {
            if ($this instanceof $indexer) {
                return $code;
            }
        }

        throw new NotFoundException(__('Unregistered Entity Indexer "%1"', get_class($this)));
    }

    /**
     * @param DataObject $entityItem
     * @return int
     */
    abstract protected function getEntityId(DataObject $entityItem): ?int;

    /**
     * @return string
     */
    private function getEntityIdField(): string
    {
        return '__uid';
    }

    /**
     * @return string
     */
    private function getEntityTypeField(): string
    {
        return '__type';
    }

    /**
     * @param DataObject $entityItem
     * @return string
     * @throws NotFoundException
     */
    private function getEntityUniqueId(DataObject $entityItem): string
    {
        return $this->getEntityType() . '_' . $this->getEntityId($entityItem);
    }

    /**
     * @param array $items
     * @param string $indexName
     */
    protected function indexItems($items, $indexName)
    {
        $this->indexManagement->indexItems($items, $indexName);
    }

    /**
     * @param array $ids
     * @param string $indexName
     */
    protected function deleteItemsFromIndex($ids, $indexName)
    {
        $this->indexManagement->deleteItems($ids, $indexName);
    }
}
