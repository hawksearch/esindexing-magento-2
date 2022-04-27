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
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypePoolInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var HierarchyManagementInterface
     */
    private $hierarchyManagement;

    /**
     * HierarchyEntityIndexer constructor.
     * @param IndexingConfig $indexingConfig
     * @param EntityTypePoolInterface $entityTypePool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param HierarchyManagementInterface $hierarchyManagement
     */
    public function __construct(
        IndexingConfig $indexingConfig,
        EntityTypePoolInterface $entityTypePool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        HierarchyManagementInterface $hierarchyManagement
    ) {
        parent::__construct(
            $indexingConfig,
            $entityTypePool,
            $indexManagement,
            $eventManager,
            $loggerFactory,
            $storeManager,
            $indexingContext
        );
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
        $category = $item;
        if ($category->getId()) {
            while ($category->getLevel() != 0) {
                if (!$category->getIsActive()) {
                    return false;
                }
                $category = $category->getParentCategory();
            }

            return true;
        }

        return false;
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
