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

namespace HawkSearch\EsIndexing\Model\Indexing;

use HawkSearch\Connector\Compatibility\PublicMethodDeprecationTrait;
use HawkSearch\Connector\Compatibility\PublicPropertyDeprecationTrait;
use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Model\Indexing\Field\NameProviderInterface as FieldNameProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @api
 * @since 0.8.0
 *
 * @template TItem of DataObject
 */
abstract class AbstractEntityRebuild implements EntityRebuildInterface
{
    use PublicMethodDeprecationTrait;
    use PublicPropertyDeprecationTrait;

    private array $deprecatedMethods = [
        'getAttributeValue' => [
            'since' => '0.7.0',
            'replacement' => __CLASS__ . '::getFieldValue()',
            'description' => 'In favour of a new Field Handlers logic'
        ],
        'getIndexedAttributes' => [
            'since' => '0.7.0',
            'replacement' => FieldNameProviderInterface::class,
            'description' => "The method will be removed. Using of 'code' and 'value' options is deprecated. Use " . FieldHandlerInterface::class . " to migrate fields with values."
        ]
    ];
    private array $deprecatedPublicProperties = [
        'entityTypePool' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via constructor injection.'
        ],
        'eventManager' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via constructor injection.'
        ],
        'hawkLogger' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via $loggerFactory constructor injection.'
        ],
        'storeManager' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via constructor injection.'
        ],
        'indexingContext' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via constructor injection.'
        ],
    ];

    /**
     * @var array<string, int>
     */
    private array $itemsToRemoveCache = [];
    /**
     * @var array<string, int>
     */
    private array $itemsToIndexCache = [];
    private EntityTypeInterface $entityType;
    /**
     * @var EntityTypePoolInterface<string, EntityTypeInterface>
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private EntityTypePoolInterface $entityTypePool;
    /**
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private EventManagerInterface $eventManager;
    /**
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private LoggerInterface $hawkLogger;
    /**
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private StoreManagerInterface $storeManager;
    /**
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private ContextInterface $indexingContext;
    private ObjectHelper $objectHelper;

    /**
     * AbstractEntityRebuild constructor.
     *
     * @param EntityTypePoolInterface<string, EntityTypeInterface> $entityTypePool
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
    ) {
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
     * @param TItem $item
     * @return bool
     */
    abstract protected function isAllowedItem(DataObject $item): bool;

    /**
     * Check if item is new or existing one.
     * By default, it is considered that new and existing items are updated through the same indexing endpoint.
     *
     * @param TItem $item
     * @return bool
     */
    protected function isItemNew(DataObject $item): bool
    {
        return true;
    }

    /**
     * @param TItem $entityItem
     * @return int
     */
    abstract protected function getEntityId(DataObject $entityItem): ?int;

    /**
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
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
     * @param array<int>|null $entityIds Entity ids for partial rebuild or null to rebuild all entities
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     * @todo $entityIds: default value null -> []
     * @todo $entityIds: change type ?array -> array
     */
    protected function rebuildBatch(SearchCriteriaInterface $searchCriteria, ?array $entityIds = null)
    {
        $pageSize = $searchCriteria->getPageSize() ?? 0;
        $currentPage = $searchCriteria->getCurrentPage() ?? 1;

        $storeId = (int)$this->storeManager->getStore()->getId();
        $this->hawkLogger->info(
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

        $this->hawkLogger->info(
            sprintf(
                "Collected %d items",
                count($items)
            )
        );

        if (!($indexName = $this->indexingContext->getIndexName($storeId))) {
            $e = new LocalizedException(
                __('There is no index selected. Please run full reindexing and try again.')
            );
            $this->hawkLogger->error("Termitating index rebuild with exception:", ['exception' => $e]);

            throw $e;
        }

        $this->hawkLogger->info(
            sprintf(
                "Picked index: %s",
                $indexName
            )
        );

        $itemsToRemove = $this->getItemsToRemove($items, $entityIds);
        $this->hawkLogger->info(
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

        $this->hawkLogger->info(
            sprintf(
                "Items to be added: %s",
                implode(',', array_keys($itemsToIndexNew))
            )
        );
        $this->addIndexItems($itemsToIndexNew, $indexName);

        $this->hawkLogger->info(
            sprintf(
                "Items to be updated: %s",
                implode(',', array_keys($itemsToIndex))
            )
        );
        $this->updateIndexItems($itemsToIndex, $indexName);
    }

    /**
     * @param TItem[] $fullItemsList Full list of items to be indexed
     * @param array<int>|null $entityIds List of entity IDs used for items selection
     * @return array<int, string>
     * @throws NotFoundException
     * @todo $entityIds: default value null -> []
     * @todo $entityIds: change type ?array -> array
     */
    protected function getItemsToRemove(array $fullItemsList, ?array $entityIds = null): array
    {
        $idsToRemove = is_array($entityIds)
            ? array_combine(
                $entityIds,
                array_map(function ($id) {
                    return $this->addTypePrefix((string)$id);
                }, $entityIds)
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
     * @param TItem[] $fullItemsList Full list of items to be indexed
     * @param array<int>|null $entityIds List of entity IDs used for items selection
     * @return array<int, TItem>
     * @throws LocalizedException
     * @todo $entityIds: remove unused argument
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
     * @param TItem $item
     * @return array<array-key, list<mixed>>
     * @throws LocalizedException
     */
    protected function convertEntityToIndexDataArray(DataObject $item): array
    {
        $itemData = [];
        $itemData[$this->getEntityIdField()] = $this->getEntityUniqueId($item);
        $itemData[$this->getEntityTypeField()] = $this->getEntityType()->getTypeName();
        $itemData = array_merge($itemData, $this->processFields($item));

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
     * @param TItem $item
     * @return array
     * @throws NotFoundException
     */
    private function processFields(DataObject $item): array
    {
        $itemData = [];
        if (method_exists($this->getEntityType(), 'getFieldNameProvider')) {
            foreach ($this->getEntityType()->getFieldNameProvider()->getList() as $fieldName => $fieldOptions) {
                $itemData[$fieldName] = $this->getAttributeValueDeprecatedWrapper($item, $fieldName);
            }
        }

        $itemData = array_merge($itemData, $this->processDeprecatedAttributes($item));
        return $itemData;
    }

    /**
     * Temporary method to overcome deprecation of getIndexedAttributes() method
     *
     * @param TItem $item
     * @return array
     * @throws NotFoundException
     */
    private function processDeprecatedAttributes(DataObject $item): array
    {
        $itemData = [];
        if (!$this->isMethodOverwritten('getIndexedAttributes')) {
            return $itemData;
        }

        $this->triggerDerivedMethodDeprecationMessage('getIndexedAttributes');
        foreach ($this->getIndexedAttributes($item) as $attribute) {
            if (!$attribute) {
                continue;
            }
            if (is_array($attribute) && !empty($attribute['code'])) {
                $itemData[$attribute['code']] = $attribute['value'] ?? null;
            } else {
                $itemData[$attribute] = $this->getAttributeValueDeprecatedWrapper($item, $attribute);
            }
        }

        return $itemData;
    }

    /**
     * @return list<mixed>|null
     */
    protected function castAttributeValue(mixed $value)
    {
        if ($value === '') {
            $value = null;
        }

        if (is_array($value)) {
            $value = array_filter($value, function ($item) {
                return $item !== '' && $item !== null;
            });
            $value = array_values($value);
        }

        return $value !== null && !is_array($value) ? [$value] : $value;
    }

    /**
     * @param TItem|null $item
     * @return array
     * @deprecated 0.7.0 method will be removed.
     *      Using of 'code' and 'value' options is deprecated. Use @see FieldHandlerInterface to migrate fields with
     *     values.
     * @see FieldNameProviderInterface
     */
    protected function getIndexedAttributes(DataObject $item = null): array // @phpstan-ignore-line
    {
        return [];
    }

    /**
     * @param TItem $item
     * @param string $fieldName
     * @return mixed
     * @throws NotFoundException
     */
    private function getFieldValue(DataObject $item, string $fieldName)
    {
        $entityType = $this->getEntityType();
        if (method_exists($entityType, 'getFieldHandler')) {
            return $entityType->getFieldHandler()->handle($item, $fieldName);
        } else {
            return $entityType->getAttributeHandler()->handle($item, $fieldName);
        }
    }

    /**
     * Temporary function to call deprecated getAttributeValue()
     *
     * @param TItem $item
     * @param string $attribute
     * @return mixed
     * @throws NotFoundException
     */
    private function getAttributeValueDeprecatedWrapper(DataObject $item, string $attribute)
    {
        if ($this->isMethodOverwritten('getAttributeValue')) {
            $this->triggerDerivedMethodDeprecationMessage('getAttributeValue');
            $itemDataAttributeValue = $this->getAttributeValue($item, $attribute);
        } else {
            $itemDataAttributeValue = $this->getFieldValue($item, $attribute);
        }

        return $itemDataAttributeValue;
    }

    /**
     * @param TItem $item
     * @param string $attribute
     * @return mixed
     * @throws NotFoundException
     * @deprecated 0.7.0 method will be removed
     * @see self::getFieldValue()
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        $this->triggerPublicMethodDeprecationMessage(__FUNCTION__);
        return $this->getFieldValue($item, $attribute);
    }

    /**
     * @throws NotFoundException
     * @todo Refactor and get rid of iterating EntityTypePool
     */
    protected function getEntityType(): EntityTypeInterface
    {
        if (!isset($this->entityType)) {
            foreach ($this->entityTypePool->getList() as $entityType) {
                $rebuilder = $entityType->getRebuilder();
                if ($this instanceof $rebuilder) {
                    break;
                }

                //check if $rebuilder is instance of Proxy class
                $proxyPosition = strlen(get_class($rebuilder)) - strlen('\Proxy');
                if (strpos(get_class($rebuilder), '\Proxy', -$proxyPosition) === $proxyPosition) {
                    $parentClass = get_parent_class($rebuilder);
                    if ($this instanceof $parentClass || is_subclass_of($this, $parentClass)) {
                        break;
                    }
                }
            }

            if (empty($entityType)) {
                throw new NotFoundException(__('Unregistered Entity Indexer "%1"', get_class($this)));
            }

            $this->entityType = $this->entityTypePool->create($entityType->getTypeName());
        }

        return $this->entityType;
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
     * @param TItem $entityItem
     * @return string
     * @throws NotFoundException
     */
    protected function getEntityUniqueId(DataObject $entityItem): string
    {
        return $this->addTypePrefix((string)$this->getEntityId($entityItem));
    }

    /**
     * @return string
     * @throws NotFoundException
     */
    protected function addTypePrefix(string $value)
    {
        return $this->getEntityType()->getUniqueId($value);
    }

    /**
     * @param TItem[] $items Key of an array item is item ID
     * @param string $indexName
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    protected function addIndexItems(array $items, string $indexName)
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
     * @param TItem[] $items Key of an array item is item ID
     * @param string $indexName
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    protected function updateIndexItems(array $items, string $indexName)
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
     * @param string[] $ids
     * @param string $indexName
     * @return void
     * @throws NotFoundException
     */
    protected function deleteIndexItems(array $ids, string $indexName)
    {
        if (!$ids) {
            return;
        }

        $this->getEntityType()->getItemsIndexer()->delete($ids, $indexName);
    }
}
