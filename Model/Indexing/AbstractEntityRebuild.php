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

namespace HawkSearch\EsIndexing\Model\Indexing;

use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractEntityRebuild implements EntityRebuildInterface
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
     * @var EntityTypePoolInterface
     */
    protected $entityTypePool;

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
     * @var ObjectHelper
     */
    private $objectHelper;

    /**
     * AbstractEntityRebuild constructor.
     *
     * @param EntityTypePoolInterface $entityTypePool
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param ObjectHelper $objectHelper
     */
    public function __construct(
        EntityTypePoolInterface $entityTypePool,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        ObjectHelper $objectHelper
    )
    {
        $this->entityTypePool = $entityTypePool;
        $this->eventManager = $eventManager;
        $this->hawkLogger = $loggerFactory->create();
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->objectHelper = $objectHelper;
    }

    /**
     * Check whether item is allowed to be indexed. Otherwise it should be removed.
     *
     * @param DataObject $item
     * @return bool
     */
    abstract protected function isAllowedItem(DataObject $item): bool;

    /**
     * Check if item is new or existing one.
     * By default it is considered that new and existing items are updated through the same indexing endpoint.
     *
     * @param DataObject $item
     * @return bool
     */
    protected function isItemNew(DataObject $item): bool
    {
        return true;
    }

    /**
     * @param DataObject $entityItem
     * @return int
     */
    abstract protected function getEntityId(DataObject $entityItem): ?int;

    /**
     * @inheritDoc
     */
    public function rebuild(SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->getEntityType()->getConfigHelper()->isEnabled()) {
            return;
        }
        $ids = $this->objectHelper->getSearchCriteriaFilterValue($searchCriteria, 'ids');

        if ($searchCriteria->getCurrentPage() === null) {
            $batchSize = $this->getEntityType()->getConfigHelper()->getBatchSize();
            $batches = ceil(count($ids) / $batchSize);
            $searchCriteria->setPageSize($batchSize);

            for ($page = 1; $page <= $batches; $page++) {
                $searchCriteria->setCurrentPage($page);
                $this->rebuildBatch($searchCriteria, $ids);
            }
        } else {
            $this->rebuildBatch($searchCriteria, $ids);
        }
    }


    /**
     * Rebuild one batch of Entity items
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param array|null $entityIds
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    protected function rebuildBatch(SearchCriteriaInterface $searchCriteria, ?array $entityIds = null)
    {
        $pageSize = $searchCriteria->getPageSize() ?? 0;
        $currentPage = $searchCriteria->getCurrentPage() ?? 1;

        $storeId = (int)$this->storeManager->getStore()->getId();
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

        $items = $this->getEntityType()->getItemsDataProvider()
            ->getItems($storeId, $entityIds, $currentPage, $pageSize);

        $this->hawkLogger->debug(
            sprintf(
                "Collected %d items",
                count($items)
            )
        );

        if (!($indexName = $this->indexingContext->getIndexName($storeId))) {
            $this->hawkLogger->debug("No index selected. Termitating index rebuild.");
            throw new LocalizedException(__('There is no index selected. Please run full reindexing and try again.'));
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
                "Items to be removed: %s",
                implode(',', $itemsToRemove)
            )
        );
        $this->deleteIndexItems($itemsToRemove, $indexName);

        $itemsToIndex = $this->getItemsToIndex($items, $entityIds);
        $itemsToIndexNew = [];
        foreach ($itemsToIndex as $itemId => $item) {
            if ($this->isItemNew($item)) {
                $itemsToIndexNew[$itemId] = $item;
                unset($itemsToIndex[$itemId]);
            }
        }

        $this->hawkLogger->debug(
            sprintf(
                "Items to be added: %s",
                implode(',', array_keys($itemsToIndexNew))
            )
        );
        $this->addIndexItems($itemsToIndexNew, $indexName);

        $this->hawkLogger->debug(
            sprintf(
                "Items to be updated: %s",
                implode(',', array_keys($itemsToIndex))
            )
        );
        $this->updateIndexItems($itemsToIndex, $indexName);
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
            ? array_combine(
                $entityIds,
                array_map(function($id) { return $this->addTypePrefix((string)$id);}, $entityIds)
            )
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

            if (!$this->isAllowedItem($item)) {
                $itemsToRemove[$this->getEntityId($item)] = $this->getEntityUniqueId($item);
                $this->itemsToRemoveCache[$this->getEntityUniqueId($item)] = $this->getEntityId($item);
            }
        }

        return array_merge($itemsToRemove, $idsToRemove);
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

            if ($this->isAllowedItem($item)) {
                $itemsToIndex[$this->getEntityId($item)] = $item;
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
        foreach ($this->getIndexedAttributes($item) as $attribute) {
            if (!$attribute) {
                continue;
            }
            if (is_array($attribute) && !empty($attribute['code'])) {
                $itemData[$attribute['code']] = $attribute['value'] ?? null;
            } else {
                $itemData[$attribute] = $this->getAttributeValue($item, $attribute);
            }
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
     * @param DataObject|null $item
     * @return array
     */
    protected function getIndexedAttributes(DataObject $item = null): array
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
     * @todo Refactor and get rid of iterating EntityTypePool
     */
    protected function getEntityType(): EntityTypeInterface
    {
        foreach ($this->entityTypePool->getList() as $entityType) {
            $rebuilder = $entityType->getRebuilder();
            if ($this instanceof $rebuilder) {
                return $entityType;
            }

            //check if $rebuilder is instance of Proxy class
            $proxyPosition = strlen(get_class($rebuilder)) - strlen('\Proxy');
            if (strpos(get_class($rebuilder), '\Proxy', -$proxyPosition) === $proxyPosition) {
                $parentClass = get_parent_class($rebuilder);
                if ($this instanceof $parentClass || is_subclass_of($this, $parentClass)) {
                    return $entityType;
                }
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
        return $this->getEntityType()->getUniqueId($value);
    }

    /**
     * @param array $items
     * @param string $indexName
     * @throws LocalizedException
     * @throws NotFoundException
     */
    protected function addIndexItems($items, $indexName)
    {
        if (!$items) {
            return;
        }

        $itemsToIndex = [];
        foreach ($items as $i => $item) {
            $itemsToIndex[$i] = $this->convertEntityToIndexDataArray($item);
        }

        $this->getEntityType()->getItemsIndexer()->add($itemsToIndex, $indexName);
    }

    /**
     * @param $items
     * @param $indexName
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    protected function updateIndexItems($items, $indexName)
    {
        if (!$items) {
            return;
        }

        $itemsToIndex = [];
        foreach ($items as $i => $item) {
            $itemsToIndex[$i] = $this->convertEntityToIndexDataArray($item);
        }

        $this->getEntityType()->getItemsIndexer()->update($itemsToIndex, $indexName);
    }

    /**
     * @param array $ids
     * @param string $indexName
     * @throws NotFoundException
     */
    protected function deleteIndexItems($ids, $indexName)
    {
        if (!$ids) {
            return;
        }

        $this->getEntityType()->getItemsIndexer()->delete($ids, $indexName);
    }
}
