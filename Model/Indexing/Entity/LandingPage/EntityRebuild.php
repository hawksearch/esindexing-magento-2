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

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterfaceFactory;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\Custom;
use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\CustomUrl;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @phpstan-type ItemType CategoryModel
 * @extends AbstractEntityRebuild<ItemType>
 */
class EntityRebuild extends AbstractEntityRebuild
{
    private const CACHE_KEY = 'HAWKSEARCH_LP_INDEXING';
    private const CACHE_LIFETIME = 300;

    /**
     * @var LandingPageInterface[]
     */
    private array $landingPages;
    /**
     * @var array<string, LandingPageInterface>
     */
    private array $customFieldMap;
    /**
     * @var array<string, LandingPageInterface>
     */
    private array $customUrlMap;
    private Cache $cache;
    private SerializerInterface $serializer;
    private LandingPageManagementInterface $landingPageManagement;
    private LandingPageInterfaceFactory $landingPageFactory;
    private CustomUrl $customUrlHandler;

    /**
     * @param EntityTypePoolInterface<string, EntityTypeInterface> $entityTypePool
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

    protected function isAllowedItem(DataObject $item): bool
    {
        //@todo check if requested category is selected to be as landing page
        return $this->isCategoryAllowed($item);
    }

    /**
     * @param ItemType $item
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
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    protected function isItemNew(DataObject $item): bool
    {
        $customUrl = $this->customUrlHandler->handle($item, LandingPageInterface::FIELD_CUSTOM_URL);

        return !array_key_exists($this->getEntityUniqueId($item), $this->getCustomFieldMap())
            && !array_key_exists($customUrl, $this->getCustomUrlMap());
    }

    protected function getEntityId(DataObject $entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getLandingPages()
    {
        if (!isset($this->landingPages)) {
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
                $this->serializer->serialize(array_map(
                    function ($page) {
                        return $page->__toArray();
                    },
                    $landingPages
                )),
                $cacheKey,
                [],
                self::CACHE_LIFETIME
            );
        }
        return $landingPages;
    }

    /**
     * @return LandingPageInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCustomFieldMap()
    {
        if (!isset($this->customFieldMap)) {
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
     * @return LandingPageInterface[]
     * @throws NoSuchEntityException
     */
    protected function getCustomUrlMap()
    {
        if (!isset($this->customUrlMap)) {
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
     * Delete Landing pages by Custom field ids
     *
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    protected function deleteIndexItems(array $ids, string $indexName)
    {
        $pageIds = [];
        $customFieldMap = $this->getCustomFieldMap();
        foreach ($ids as $i => $id) {
            if (!array_key_exists($id, $customFieldMap)) {
                unset($ids[$i]);
                continue;
            }

            $pageIds[] = (string)$customFieldMap[$id]->getPageId();
        }

        parent::deleteIndexItems($pageIds, $indexName);
    }

    protected function addTypePrefix(string $value)
    {
        return Custom::CUSTOM_FIELD_PREFIX . $value;
    }

    protected function castAttributeValue(mixed $value)
    {
        return $value;
    }

    /**
     * @param ItemType[] $items
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

    protected function addIndexItems(array $items, string $indexName)
    {
        if (!$items) {
            return;
        }

        $this->getEntityType()->getItemsIndexer()->add($this->convertItems($items), $indexName);
    }

    protected function updateIndexItems(array $items, string $indexName)
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
            if ($current !== null) {
                $convertedItem->setPageId($current->getPageId());
                $resultItemsToUpdate[] = $convertedItem;
            }

            /** @var LandingPageInterface $last */
            $last = array_shift($existingPages);
            if ($last !== null) {
                $last->setCustom(null);
                $resultItemsToUpdate[] = $last;
            }
        }


        $this->getEntityType()->getItemsIndexer()->update($resultItemsToUpdate, $indexName);
    }
}
