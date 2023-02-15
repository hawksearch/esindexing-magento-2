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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Hierarchy;

use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypePoolInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class EntityRebuild extends AbstractEntityRebuild
{
    /**
     * @var \HawkSearch\EsIndexing\Api\HierarchyManagementInterface
     */
    private $hierarchyManagement;

    /**
     * HierarchyEntity constructor.
     *
     * @param EntityTypePoolInterface $entityTypePool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param HierarchyManagementInterface $hierarchyManagement
     */
    public function __construct(
        EntityTypePoolInterface $entityTypePool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        ObjectHelper $objectHelper,
        HierarchyManagementInterface $hierarchyManagement
    ) {
        parent::__construct(
            $entityTypePool,
            $indexManagement,
            $eventManager,
            $loggerFactory,
            $storeManager,
            $indexingContext,
            $objectHelper
        );
        $this->hierarchyManagement = $hierarchyManagement;
    }

    /**
     * @param CategoryInterface|Category|DataObject $item
     * @inheritDoc
     */
    protected function isAllowedItem(DataObject $item): bool
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
     * @inheritDoc
     */
    protected function getIndexedAttributes(DataObject $item = null): array
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
}
