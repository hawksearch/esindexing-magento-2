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

use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\App\Emulation;

class HierarchyEntityIndexer extends AbstractEntityIndexer
{
    //TODO: replace with attributeDataProvider interface
    public const ADDITIONAL_ATTRIBUTES_HANDLERS = [
        'HierarchyId' => 'getHierarchyId',
        'Name' => 'getName',
        'ParentHierarchyId' => 'getParentHierarchyId',
        'IsActive' => 'getIsActive',
    ];

    public const PARENT_HIERARCHY_NAME = 'category';

    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var HierarchyManagementInterface
     */
    private $hierarchyManagement;

    /**
     * HierarchyEntityIndexer constructor.
     * @param IndexingConfig $indexingConfig
     * @param Emulation $emulation
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param EntityIndexerPoolInterface $entityIndexerPool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param HierarchyManagementInterface $hierarchyManagement
     * @param LoggerFactoryInterface $loggerFactory
     */
    public function __construct(
        IndexingConfig $indexingConfig,
        Emulation $emulation,
        ItemsProviderPoolInterface $itemsProviderPool,
        EntityIndexerPoolInterface $entityIndexerPool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        HierarchyManagementInterface $hierarchyManagement,
        LoggerFactoryInterface $loggerFactory
    ) {
        parent::__construct(
            $indexingConfig,
            $emulation,
            $itemsProviderPool,
            $entityIndexerPool,
            $indexManagement,
            $eventManager,
            $loggerFactory
        );
        $this->indexManagement = $indexManagement;
        $this->hierarchyManagement = $hierarchyManagement;
    }

    /**
     * @inheritDoc
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        $value = '';
        if (isset(static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute])
            && is_callable([$this, static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]])
        ) {
            $value = $this->{self::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]}($item);
        } else {
            $value = $item->getData($attribute);
        }
        return $value;
    }

    /**
     * @param CategoryInterface|Category|DataObject $item
     * @inheritDoc
     */
    protected function canItemBeIndexed(DataObject $item): bool
    {
        if (!$item->getIsActive()) {
            return false;
        }
        //@TODO Check if parent categories are active

        return true;
    }

    /**
     * @param CategoryInterface|Category|DataObject $entityItem
     * @inheritDoc
     */
    protected function getEntityId($entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @return array
     */
    protected function getIndexedAttributes(): array
    {
        return [
            'HierarchyId',
            'Name',
            'ParentHierarchyId',
            'IsActive',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function castAttributeValue($value)
    {
        return $value === '' ? null : $value;
    }

    /**
     * @inheritdoc
     */
    protected function indexItems($items, $indexName)
    {
        $this->hierarchyManagement->upsertHierarchy($items, $indexName);
    }

    /**
     * @inheritdoc
     */
    protected function deleteItemsFromIndex($ids, $indexName)
    {
        $this->hierarchyManagement->deleteHierarchyItems($ids, $indexName);
    }

    /**
     * @param CategoryInterface $category
     * @return int
     */
    private function getHierarchyId(CategoryInterface $category)
    {
        return $this->getEntityId($category);
    }

    /**
     * @param CategoryInterface $category
     * @return string|null
     */
    private function getName(CategoryInterface $category)
    {
        if ($category->getLevel() == 1) {
            return static::PARENT_HIERARCHY_NAME;
        } else {
            return $category->getName();
        }
    }

    /**
     * @param CategoryInterface $category
     * @return int
     */
    private function getParentHierarchyId(CategoryInterface $category)
    {
        if ($category->getLevel() == 1) {
            return 0;
        } else {
            return (int)$category->getParentId();
        }
    }

    /**
     * @param CategoryInterface $category
     * @return bool
     */
    private function getIsActive(CategoryInterface $category)
    {
        return (bool)$category->getIsActive();
    }
}
