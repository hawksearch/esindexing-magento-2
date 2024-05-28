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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\LandingPage;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterfaceFactory;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\CustomUrl;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class EntityRebuild extends AbstractEntityRebuild
{
    private const CACHE_KEY = 'HAWKSEARCH_LP_INDEXING';
    private const CACHE_LIFETIME = 300;
    private const CUSTOM_FIELD_PREFIX = "__mage_catid__";

    /**
     * @var LandingPageInterface[]
     */
    private $landingPages;

    /**
     * @var array
     */
    private $customFieldMap;

    /**
     * @var array
     */
    private $customUrlMap;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LandingPageManagementInterface
     */
    private $landingPageManagement;

    /**
     * @var LandingPageInterfaceFactory
     */
    private $landingPageFactory;

    /**
     * @var CustomUrl
     */
    private $customUrlHandler;

    /**
     * @param EntityTypePoolInterface $entityTypePool
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param ObjectHelper $objectHelper
     * @param Cache $cache
     * @param SerializerInterface $serializer
     * @param LandingPageManagementInterface $landingPageManagement
     * @param LandingPageInterfaceFactory $landingPageFactory
     * @param CustomUrl $customUrlHandler
     */
    public function __construct(
        EntityTypePoolInterface $entityTypePool,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        ObjectHelper $objectHelper,
        Cache $cache,
        SerializerInterface $serializer,
        LandingPageManagementInterface $landingPageManagement,
        LandingPageInterfaceFactory $landingPageFactory,
        CustomUrl $customUrlHandler
    ) {
        parent::__construct(
            $entityTypePool,
            $eventManager,
            $loggerFactory,
            $storeManager,
            $indexingContext,
            $objectHelper
        );
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->landingPageManagement = $landingPageManagement;
        $this->landingPageFactory = $landingPageFactory;
        $this->customUrlHandler = $customUrlHandler;
    }

    /**
     * @param CategoryInterface|Category|DataObject $item
     * @inheritDoc
     */
    protected function isAllowedItem(DataObject $item): bool
    {
        //@todo check if requested category is selected to be as landing page
        return $this->isCategoryAllowed($item);
    }

    /**
     * @param CategoryInterface $item
     * @return bool
     */
    protected function isCategoryAllowed(CategoryInterface $item)
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
     * @inheritDoc
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    protected function isItemNew(DataObject $item): bool
    {
        $customUrl = $this->customUrlHandler->handle($item, LandingPageInterface::FIELD_CUSTOM_URL);

        return !array_key_exists($this->getEntityUniqueId($item), $this->getCustomFieldMap())
            && !array_key_exists($customUrl, $this->getCustomUrlMap());
    }

    /**
     * @param CategoryInterface|Category|DataObject $entityItem
     * @inheritDoc
     */
    protected function getEntityId(DataObject $entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     */
    protected function getIndexedAttributes(DataObject $item = null): array
    {
        return [
            LandingPageInterface::FIELD_NAME,
            LandingPageInterface::FIELD_CUSTOM_URL,
            LandingPageInterface::FIELD_NARROW_XML,
            LandingPageInterface::FIELD_PAGE_ID,
            LandingPageInterface::FIELD_CUSTOM_SORT_LIST,
            [
                'code' => LandingPageInterface::FIELD_CUSTOM,
                'value' => $this->getEntityUniqueId($item)
            ],
            [
                'code' => LandingPageInterface::FIELD_IS_FACET_OVERRIDE,
                'value' => false
            ],
            [
                'code' => LandingPageInterface::FIELD_PAGE_TYPE,
                'value' => 'ProductListing'
            ],
        ];
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getLandingPages()
    {
        if ($this->landingPages === null) {
            $this->landingPages = $this->landingPageManagement->getLandingPages();
        }

        return $this->landingPages;
    }

    /**
     * @return array|LandingPageInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCachedLandingPages()
    {
        $cacheKey = self::CACHE_KEY . $this->storeManager->getStore()->getId();
        if ($serialized = $this->cache->load($cacheKey)) {
            $landingPagesData = $this->serializer->unserialize($serialized);
            $landingPages = [];
            foreach ($landingPagesData as $landingPage) {
                $landingPages[] = $this->landingPageFactory->create(['data' => $landingPage]);
            }
        } else {
            $landingPages = $this->landingPageManagement->getLandingPages();
            $this->cache->save(
                $this->serializer->serialize(array_map(function($page){return $page->__toArray();}, $landingPages)),
                $cacheKey,
                [],
                self::CACHE_LIFETIME
            );
        }
        return $landingPages;
    }

    /**
     * Retreive mapping by Custom field
     *
     * @return LandingPageInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCustomFieldMap()
    {
        if ($this->customFieldMap === null) {
            $map = [];
            /** @var LandingPageInterface $item */
            foreach ($this->getLandingPages() as $item) {
                if (!empty($item->getCustom())) {
                    $map[$item->getCustom()] = $item;
                }
            }
            $this->customFieldMap = $map;
        }

        return $this->customFieldMap;
    }

    /**
     * Retreive mapping by CustomUrl field
     *
     * @return LandingPageInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCustomUrlMap()
    {
        if ($this->customUrlMap === null) {
            $map = [];
            /** @var LandingPageInterface $item */
            foreach ($this->getLandingPages() as $item) {
                if (!empty($item->getCustomUrl())) {
                    $map[$item->getCustomUrl()] = $item;
                }
            }
            $this->customUrlMap = $map;
        }

        return $this->customUrlMap;
    }

    /**
     * @param array $ids Landing page Custom field array
     * @param string $indexName
     * @return void
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    protected function deleteIndexItems($ids, $indexName)
    {
        $pageIds = [];
        $customFieldMap = $this->getCustomFieldMap();
        foreach ($ids as $i => $id) {
            if (!array_key_exists($id, $customFieldMap)) {
                unset($ids[$i]);
                continue;
            }

            $pageIds[] = $customFieldMap[$id]->getPageId();
        }

        parent::deleteIndexItems($pageIds, $indexName);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function addTypePrefix(string $value)
    {
        return self::CUSTOM_FIELD_PREFIX . $value;
    }

    /**
     * @inheritdoc
     */
    protected function castAttributeValue($value)
    {
        return $value;
    }

    /**
     * @param array $items
     * @return LandingPageInterface[]
     * @throws LocalizedException
     */
    protected function convertItems(array $items)
    {
        $itemsToIndex = [];
        foreach ($items as $i => $item) {
            $itemsToIndex[$i] = $this->landingPageFactory->create(
                ['data' => $this->convertEntityToIndexDataArray($item)]
            );
        }

        return $itemsToIndex;
    }

    /**
     * @inheritDoc
     */
    protected function addIndexItems($items, $indexName)
    {
        if (!$items) {
            return;
        }

        $this->getEntityType()->getItemsIndexer()->add($this->convertItems($items), $indexName);
    }

    /**
     * @inheritDoc
     */
    protected function updateIndexItems($items, $indexName)
    {
        if (!$items) {
            return;
        }

        $convertedItems = $this->convertItems($items);
        $resultItemsToUpdate = [];
        $customFieldMap = $this->getCustomFieldMap();
        $customUrlMap = $this->getCustomUrlMap();

        foreach ($convertedItems as $convertedItem) {
            $existingPages = [];
            $pageByCustomUrl = $customUrlMap[$convertedItem->getCustomUrl()] ?? null;
            $existingPages[] = $pageByCustomUrl;
            $pageByCustomField = $customFieldMap[$convertedItem->getCustom()] ?? null;
            if ($pageByCustomField) {
                if (!$pageByCustomUrl || ($pageByCustomField->getPageId() != $pageByCustomUrl->getPageId())) {
                    $existingPages[] = $pageByCustomField;
                }
            }
            $existingPages = array_filter($existingPages);

            /** @var LandingPageInterface $current */
            $current = array_shift($existingPages);
            if ($current){
                $convertedItem->setPageId($current->getPageId());
                $resultItemsToUpdate[] = $convertedItem;
            }

            /** @var LandingPageInterface $last */
            $last = array_shift($existingPages);
            if ($last) {
                $last->setCustom(null);
                $resultItemsToUpdate[] = $last;
            }
        }


        $this->getEntityType()->getItemsIndexer()->update($resultItemsToUpdate, $indexName);
    }
}
