<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\EsIndexing\Model\Config\General;
use HawkSearch\EsIndexing\Model\Config\Products as ProductsConfig;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use HawkSearch\EsIndexing\Model\Product\Attributes as ProductAttributes;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ProductEntityIndexer implements EntityIndexerInterface
{
    //TODO: replace with attributeDataProvider interface
    public const ADDITIONAL_ATTRIBUTES_HANDLERS = [
        'url' => 'getUrl',
        'group_id' => 'getGroupId',
        'thumbnail_url' => 'getThumbnailUrl',
        'image_url' => 'getImageUrl',
    ];

    /**
     * @var array
     */
    private $itemsToRemoveCache = [];

    /**
     * @var array
     */
    private $itemsToIndexCache = [];

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var ItemsProviderPoolInterface
     */
    private $itemsProviderPool;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @var Configuration
     */
    private $catalogInventoryConfiguration;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var ProductsConfig
     */
    private $attributesConfigProvider;

    /**
     * @var ProductAttributes
     */
    private $productAttributes;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * @var General
     */
    private $generalConfig;

    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * ProductEntityIndexer constructor.
     * @param Emulation $emulation
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param Visibility $visibility
     * @param Configuration $catalogInventoryConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param Json $jsonSerializer
     * @param ProductsConfig $attributesConfigProvider
     * @param ProductAttributes $productAttributes
     * @param StoreManagerInterface $storeManager
     * @param ProductDataProvider $productDataProvider
     * @param ImageHelper $imageHelper
     * @param InstructionManagerPool $instructionManagerPool
     * @param General $generalConfig
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        Emulation $emulation,
        ItemsProviderPoolInterface $itemsProviderPool,
        Visibility $visibility,
        Configuration $catalogInventoryConfiguration,
        StockRegistryInterface $stockRegistry,
        Json $jsonSerializer,
        ProductsConfig $attributesConfigProvider,
        ProductAttributes $productAttributes,
        StoreManagerInterface $storeManager,
        ProductDataProvider $productDataProvider,
        ImageHelper $imageHelper,
        InstructionManagerPool $instructionManagerPool,
        General $generalConfig,
        IndexManagementInterface $indexManagement
    ) {
        $this->emulation = $emulation;
        $this->itemsProviderPool = $itemsProviderPool;
        $this->visibility = $visibility;
        $this->catalogInventoryConfiguration = $catalogInventoryConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->jsonSerializer = $jsonSerializer;
        $this->attributesConfigProvider = $attributesConfigProvider;
        $this->productAttributes = $productAttributes;
        $this->storeManager = $storeManager;
        $this->productDataProvider = $productDataProvider;
        $this->imageHelper = $imageHelper;
        $this->instructionManagerPool = $instructionManagerPool;
        $this->generalConfig = $generalConfig;
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

        if ($entityIds === null) {
            // full reindex
            // TODO:
        } else {
            // update delta index
        }
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function rebuildEntityIndexBatch(int $storeId, int $currentPage, int $pageSize, ?array $entityIds = null)
    {
        if (!$this->generalConfig->isIndexingEnabled($storeId)) {
            return;
        }
        //TODO: add logging

        //$this->emulation->startEnvironmentEmulation($storeId);

        try {
            $items = $this->itemsProviderPool->get('products')->getItems($storeId, $entityIds, $currentPage, $pageSize);

            $itemsToRemove = $this->getItemsToRemove($items, $entityIds);
            $this->removeItemsFromIndex($itemsToRemove, $this->getIndexName());

            $itemsToIndex = $this->getItemsToIndex($items, $entityIds);
            $this->addItemsToIndex($itemsToIndex, $this->getIndexName());


        } catch (\Exception $e) {
            $this->emulation->stopEnvironmentEmulation();
            throw $e;
        }
        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @param bool $useCurrent
     * @return string
     */
    private function getIndexName($useCurrent = false)
    {
        $this->indexManagement->switchIndices();
        return $this->indexManagement->getIndexName($useCurrent);
    }

    /**
     * @param array $fullItemsList Full list of items to be indexed
     * @param array|null $entityIds List of entity IDs used for items selection
     */
    private function getItemsToIndex(array $fullItemsList, ?array $entityIds = null)
    {
        $itemsToIndex = [];

        foreach ($fullItemsList as $item) {
            if (isset($itemsToIndex[$item->getId()])
                || isset($this->itemsToIndexCache[$item->getId()])
                || isset($this->itemsToRemoveCache[$item->getId()])
            ) {
                continue;
            }

            if ($this->canItemBeIndexed($item)) {
                $itemsToIndex[$item->getId()] = $this->convertEntityToIndexDataArray($item);
                $this->itemsToIndexCache[$item->getId()] = $item->getId();
            }
        }

        return $itemsToIndex;
    }

    /**
     * @param ProductInterface[] $fullItemsList Full list of items to be indexed
     * @param array|null $entityIds List of entity IDs used for items selection
     * @return array
     */
    private function getItemsToRemove(array $fullItemsList, ?array $entityIds = null)
    {
        $idsToRemove = is_array($entityIds) ? array_combine($entityIds, $entityIds) : [];
        $itemsToRemove = [];

        foreach ($fullItemsList as $item) {
            // Don't remove item from the index if it is in the list of indexed items
            if (isset($idsToRemove[$item->getId()])) {
                unset($idsToRemove[$item->getId()]);
            }

            if (isset($itemsToRemove[$item->getId()])
                || isset($this->itemsToRemoveCache[$item->getId()])
                || isset($this->itemsToIndexCache[$item->getId()])
            ) {
                continue;
            }

            if (!$this->canItemBeIndexed($item)) {
                $itemsToRemove[$item->getId()] = $item->getId();
                $this->itemsToRemoveCache[$item->getId()] = $item->getId();
            }
        }

        $itemsToRemove = array_merge($itemsToRemove, $idsToRemove);

        return $itemsToRemove;
    }

    /**
     * @param ProductInterface|Product $product
     * @param bool $isChild
     * @return bool
     */
    private function canItemBeIndexed(ProductInterface $product, $isChild = false)
    {
        if ($product->isDeleted()) {
            return false;
        }

        if ($product->getStatus() == Status::STATUS_DISABLED) {
            return false;
        }

        if (!$isChild && !in_array($product->getVisibility(), $this->visibility->getVisibleInSiteIds())) {
            return false;
        }

        $isInStock = true;
        if (!$this->catalogInventoryConfiguration->isShowOutOfStock()) {
            $isInStock = $this->isProductInStock($product);
        }

        if (!$isInStock) {
            return false;
        }

        return true;
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    private function isProductInStock(ProductInterface $product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        return $stockItem->getIsInStock();
    }

    /**
     * @param ProductInterface $item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function convertEntityToIndexDataArray(ProductInterface $item)
    {
        $itemData = [];

        foreach ($this->getIndexedAttributes() as $attribute) {
            if (!$attribute) {
                continue;
            }
            $attributeValues = $this->getAttributeValues($item, $attribute);
            if (!count($attributeValues)) {
                continue;
            }
            $itemData[$attribute] = $attributeValues;
        }
        return $itemData;
    }

    /**
     * @return array
     */
    private function getIndexedAttributes()
    {
        $currentAttributesConfig = $this->jsonSerializer->unserialize(
            $this->attributesConfigProvider->getAttributes()
        );

        $attributes = [];
        foreach ($currentAttributesConfig as $configItem) {
            if (isset($configItem['attribute'])) {
                $attributes[] = $configItem['attribute'];
            }
        }

        return array_merge($attributes, $this->productAttributes->getMandatoryAttributes());
    }

    /**
     * @param ProductInterface|Product $product
     * @param string $attribute
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeValues(ProductInterface $product, string $attribute)
    {
        $value = '';

        if (isset(static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute])
            && is_callable([$this, static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]])
        ) {
            $value = $this->{self::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]}($product);
        } else {

            /** @var ProductResource $productResource */
            $productResource = $product->getResource();

            /** @var AttributeResource $attributeResource */
            $attributeResource = $productResource->getAttribute($attribute);
            if ($attributeResource) {
                $attributeResource->setData('store_id', $product->getStoreId());

                $value = $product->getData($attribute);

                if ($value !== null) {
                    if (!is_array($value) && $attributeResource->usesSource()) {
                        $value = $product->getAttributeText($attribute);
                    }

                    if (!$value) {
                        $value = $attributeResource->getFrontend()->getValue($product);
                    }
                }
            }
        }

        if ($value === '') {
            $value = null;
        }

        return $value === null ? [] : array($value);
    }

    /**
     * @param array $items
     * @param string $indexName
     * @throws \HawkSearch\Connector\Gateway\InstructionException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function addItemsToIndex(array $items, $indexName)
    {
        $data = [
            'IndexName' => $indexName,
            'Items' => array_values($items)
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('indexItems', $data)->get();

    }

    /**
     * @param array $ids
     * @param $indexName
     */
    private function removeItemsFromIndex(array $ids, $indexName)
    {
        $data = [
            'IndexName' => $indexName,
            'Ids' => $ids
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteItems', $data)->get();
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getUrl(ProductInterface $product)
    {
        $store = $this->storeManager->getStore($product->getStoreId());
        return substr($product->getProductUrl(true), strlen($store->getBaseUrl()));
    }

    /**
     * Return a comma separated list of product parent ids or product id
     * @param ProductInterface $product
     * @return string
     */
    private function getGroupId(ProductInterface $product)
    {
        $ids = $this->productDataProvider->getParentProductIds([$product->getId()]);
        if (!$ids) {
            $ids = [$product->getId()];
        }

        return implode(",",$ids);
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getThumbnailUrl(ProductInterface $product)
    {
        $store = $this->storeManager->getStore($product->getStoreId());
        return substr(
            $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl(),
            strlen($store->getBaseUrl())
        );
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImageUrl(ProductInterface $product)
    {
        $store = $this->storeManager->getStore($product->getStoreId());
        return substr(
            $this->imageHelper->init($product, 'product_base_image')->getUrl(),
            strlen($store->getBaseUrl())
        );
    }
}
