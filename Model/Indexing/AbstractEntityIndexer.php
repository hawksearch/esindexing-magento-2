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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Indexing;

use Exception;
use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypePoolInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
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
    protected $indexingConfig;

    /**
     * @var EntityTypePoolInterface
     */
    protected $entityTypePool;

    /**
     * @var IndexManagementInterface
     */
    protected $indexManagement;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var LoggerInterface
     */
    protected $hawkLogger;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ContextInterface
     */
    protected $indexingContext;

    /**
     * AbstractEntityIndexer constructor.
     * @param IndexingConfig $indexingConfig
     * @param EntityTypePoolInterface $entityTypePool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     */
    public function __construct(
        IndexingConfig $indexingConfig,
        EntityTypePoolInterface $entityTypePool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext
    )
    {
        $this->indexingConfig = $indexingConfig;
        $this->entityTypePool = $entityTypePool;
        $this->indexManagement = $indexManagement;
        $this->eventManager = $eventManager;
        $this->hawkLogger = $loggerFactory->create();
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
    }

    /**
     * @param DataObject $item
     * @return bool
     */
    abstract protected function canItemBeIndexed(DataObject $item): bool;

    /**
     * @param DataObject $entityItem
     * @return int
     */
    abstract protected function getEntityId(DataObject $entityItem): ?int;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function rebuildEntityIndex($entityIds = null)
    {
        if (!$this->indexingConfig->isIndexingEnabled()) {
            return;
        }

        $batchSize = $this->indexingConfig->getItemsBatchSize();
        $batches = ceil(count($entityIds) / $batchSize);

        for ($page = 1; $page <= $batches; $page++) {
            $this->rebuildEntityIndexBatch($page, $batchSize, $entityIds);
        }
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function rebuildEntityIndexBatch(int $currentPage, int $pageSize, ?array $entityIds = null)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        if (!$this->indexingConfig->isIndexingEnabled()) {
            return;
        }

        $this->hawkLogger->debug(
            sprintf(
                "Starting indexing for Entity type %s, Store %d, Page %d, Page size %d, IDs %s",
                $this->getEntityType()->getTypeName(),
                $storeId,
                $currentPage,
                $pageSize,
                implode(',', $entityIds ?? [])
            )
        );

        $items = $this->getEntityType()->getItemsProvider()
            ->getItems($storeId, $entityIds, $currentPage, $pageSize);

        $this->hawkLogger->debug(
            sprintf(
                "Collected %d items",
                count($items)
            )
        );

        if (!($indexName = $this->indexingContext->getIndexName($storeId))) {
            $isFullReindex = $entityIds === null;
            $isCurrentIndex = !$isFullReindex;
            $indexName = $this->indexManagement->getIndexName($isCurrentIndex);
        }

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
        $this->indexItems($itemsToIndex, $indexName);
    }

    /**
     * @param DataObject[] $fullItemsList Full list of items to be indexed
     * @param array|null $entityIds List of entity IDs used for items selection
     * @return array
     * @throws NotFoundException
     */
    protected function getItemsToRemove(array $fullItemsList, ?array $entityIds = null): array
    {
        $idsToRemove = is_array($entityIds)
            ? array_combine($entityIds, array_map(function($id) { return $this->addTypePrefix($id);}, $entityIds))
            : [];
        $itemsToRemove = [];

        foreach ($fullItemsList as $item) {
            // Don't remove item from the index if it is in the list of indexed items
            if (isset($idsToRemove[$this->getEntityId($item)])) {
                unset($idsToRemove[$this->getEntityId($item)]);
            }

            if (isset($itemsToRemove[$this->getEntityId($item)])
                || isset($this->itemsToRemoveCache[$this->getEntityUniqueId($item)])
                || isset($this->itemsToIndexCache[$this->getEntityUniqueId($item)])
            ) {
                continue;
            }

            if (!$this->canItemBeIndexed($item)) {
                $itemsToRemove[$this->getEntityId($item)] = $this->getEntityUniqueId($item);
                $this->itemsToRemoveCache[$this->getEntityUniqueId($item)] = $this->getEntityId($item);
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
                || isset($this->itemsToIndexCache[$this->getEntityUniqueId($item)])
                || isset($this->itemsToRemoveCache[$this->getEntityUniqueId($item)])
            ) {
                continue;
            }

            if ($this->canItemBeIndexed($item)) {
                $itemsToIndex[$this->getEntityId($item)] = $this->convertEntityToIndexDataArray($item);
                $this->itemsToIndexCache[$this->getEntityUniqueId($item)] = $this->getEntityId($item);
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
        $itemData[$this->getEntityTypeField()] = $this->getEntityType()->getTypeName();
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

        if (is_array($value)) {
            $value = array_filter($value, function ($item){
                return $item !== '' && $item !== null;
            });
            $value = array_values($value);
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
     * @throws NotFoundException
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        return $this->getEntityType()->getAttributeHandler()->handle($item, $attribute);
    }

    /**
     * @return EntityTypeInterface
     * @throws NotFoundException
     */
    protected function getEntityType(): EntityTypeInterface
    {
        foreach ($this->entityTypePool->getList() as $typeName => $entityType) {
            $entityIndexer = $entityType->getEntityIndexer();
            if ($entityIndexer instanceof $this) {
                return $entityType;
            }
        }

        throw new NotFoundException(__('Unregistered Entity Indexer "%1"', get_class($this)));
    }

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
    protected function getEntityUniqueId(DataObject $entityItem): string
    {
        return $this->addTypePrefix((string)$this->getEntityId($entityItem));
    }

    /**
     * @param string $value
     * @return string
     * @throws NotFoundException
     */
    protected function addTypePrefix(string $value)
    {
        return $this->getEntityType()->getTypeName() . '_' . $value;
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
