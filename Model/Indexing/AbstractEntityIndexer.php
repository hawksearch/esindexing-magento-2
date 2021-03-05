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
use HawkSearch\EsIndexing\Model\Config\General as GeneralConfig;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\App\Emulation;

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
     * @var GeneralConfig
     */
    private $generalConfig;

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
     * AbstractEntityIndexer constructor.
     * @param GeneralConfig $generalConfig
     * @param IndexingConfig $indexingConfig
     * @param Emulation $emulation
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param EntityIndexerPoolInterface $entityIndexerPool
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        GeneralConfig $generalConfig,
        IndexingConfig $indexingConfig,
        Emulation $emulation,
        ItemsProviderPoolInterface $itemsProviderPool,
        EntityIndexerPoolInterface $entityIndexerPool,
        IndexManagementInterface $indexManagement
    )
    {
        $this->generalConfig = $generalConfig;
        $this->indexingConfig = $indexingConfig;
        $this->emulation = $emulation;
        $this->itemsProviderPool = $itemsProviderPool;
        $this->entityIndexerPool = $entityIndexerPool;
        $this->indexManagement = $indexManagement;
    }

    /**
     * @inheritDoc
     */
    public function rebuildEntityIndex(int $storeId, $entityIds = null)
    {
        if (!$this->generalConfig->isIndexingEnabled($storeId)) {
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
        if (!$this->generalConfig->isIndexingEnabled($storeId)) {
            return;
        }
        //TODO: add logging

        $this->emulation->startEnvironmentEmulation($storeId);

        try {
            $items = $this->itemsProviderPool->get($this->getEntityType())
                ->getItems($storeId, $entityIds, $currentPage, $pageSize);

            $isFullReindex = $entityIds === null;
            $isCurrentIndex = !$isFullReindex;

            $itemsToRemove = $this->getItemsToRemove($items, $entityIds);
            $this->indexManagement->deleteItems($itemsToRemove, $this->indexManagement->getIndexName($isCurrentIndex));

            $itemsToIndex = $this->getItemsToIndex($items, $entityIds);
            $this->indexManagement->indexItems($itemsToIndex, $this->indexManagement->getIndexName($isCurrentIndex));


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
    protected function getItemsToRemove(array $fullItemsList, ?array $entityIds = null)
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
    protected function getItemsToIndex(array $fullItemsList, ?array $entityIds = null)
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
    protected function convertEntityToIndexDataArray(DataObject $item)
    {
        $itemData = [];
        $itemData['__uid'] = $this->castAttributeValue($this->getEntityUniqueId($item));
        $itemData['__type'] = $this->castAttributeValue($this->getEntityType());
        foreach ($this->getIndexedAttributes() as $attribute) {
            if (!$attribute) {
                continue;
            }
            $attributeValues = $this->castAttributeValue($this->getAttributeValue($item, $attribute));
            if ($attributeValues === null) {
                continue;
            }
            $itemData[$attribute] = $attributeValues;
        }
        return $itemData;
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    private function castAttributeValue($value) {
        if ($value === '') {
            $value = null;
        }

        return $value !== null ? array($value) : $value;
    }

    /**
     * @return array
     */
    protected function getIndexedAttributes()
    {
        return [];
    }

    /**
     * @param DataObject $item
     * @param string $attribute
     * @return array
     */
    abstract protected function getAttributeValue(DataObject $item, string $attribute);

    /**
     * @param DataObject $item
     * @return bool
     */
    abstract protected function canItemBeIndexed(DataObject $item);

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
    abstract protected function getEntityId($entityItem): ?int;

    /**
     * @return string
     */
    private function getEntityIdField()
    {
        return '__uid';
    }

    /**
     * @return string
     */
    private function getEntityTypeField()
    {
        return '__type';
    }

    /**
     * @param DataObject $entityItem
     * @return string
     * @throws NotFoundException
     */
    private function getEntityUniqueId($entityItem)
    {
        return $this->getEntityType() . '_' . $this->getEntityId($entityItem);
    }
}
