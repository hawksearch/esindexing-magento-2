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

use HawkSearch\EsIndexing\Model\Config\General as GeneralConfig;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\Config\Products as ProductsConfig;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use HawkSearch\EsIndexing\Model\Product\Attributes as ProductAttributes;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ProductEntityIndexer extends AbstractEntityIndexer
{
    //TODO: replace with attributeDataProvider interface
    public const ADDITIONAL_ATTRIBUTES_HANDLERS = [
        'url' => 'getUrl',
        'group_id' => 'getGroupId',
        'thumbnail_url' => 'getThumbnailUrl',
        'image_url' => 'getImageUrl',
    ];

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

    public function __construct(
        GeneralConfig $generalConfig,
        IndexingConfig $indexingConfig,
        Emulation $emulation,
        ItemsProviderPoolInterface $itemsProviderPool,
        EntityIndexerPoolInterface $entityIndexerPool,
        IndexManagementInterface $indexManagement,
        Visibility $visibility,
        Configuration $catalogInventoryConfiguration,
        StockRegistryInterface $stockRegistry,
        Json $jsonSerializer,
        ProductsConfig $attributesConfigProvider,
        ProductAttributes $productAttributes,
        StoreManagerInterface $storeManager,
        ProductDataProvider $productDataProvider,
        ImageHelper $imageHelper
    ) {
        parent::__construct(
            $generalConfig,
            $indexingConfig,
            $emulation,
            $itemsProviderPool,
            $entityIndexerPool,
            $indexManagement
        );

        $this->visibility = $visibility;
        $this->catalogInventoryConfiguration = $catalogInventoryConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->jsonSerializer = $jsonSerializer;
        $this->attributesConfigProvider = $attributesConfigProvider;
        $this->productAttributes = $productAttributes;
        $this->storeManager = $storeManager;
        $this->productDataProvider = $productDataProvider;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @inheritdoc
     */
    protected function getIndexedAttributes()
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
     * @param ProductInterface|Product|DataObject $item
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        $value = '';

        if (isset(static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute])
            && is_callable([$this, static::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]])
        ) {
            $value = $this->{self::ADDITIONAL_ATTRIBUTES_HANDLERS[$attribute]}($item);
        } else {

            /** @var ProductResource $productResource */
            $productResource = $item->getResource();

            /** @var AttributeResource $attributeResource */
            $attributeResource = $productResource->getAttribute($attribute);
            if ($attributeResource) {
                $attributeResource->setData('store_id', $item->getStoreId());

                $value = $item->getData($attribute);

                if ($value !== null) {
                    if (!is_array($value) && $attributeResource->usesSource()) {
                        $value = $item->getAttributeText($attribute);
                    }

                    if ($value === false) {
                        $value = $attributeResource->getFrontend()->getValue($item);
                    }
                }
            }
        }

        return $value;
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
     * @param ProductInterface|Product|DataObject $entityItem
     * @inheritdoc
     */
    protected function getEntityId($entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @param ProductInterface|Product|DataObject $item
     * @inheritdoc
     */
    protected function canItemBeIndexed(DataObject $item)
    {
        if ($item->isDeleted()) {
            return false;
        }

        if ($item->getStatus() == Status::STATUS_DISABLED) {
            return false;
        }

        $isChild = (bool)$this->productDataProvider->getParentProductIds([$item->getId()]);

        if (!$isChild && !in_array($item->getVisibility(), $this->visibility->getVisibleInSiteIds())) {
            return false;
        }

        $isInStock = true;
        if (!$this->catalogInventoryConfiguration->isShowOutOfStock()) {
            $isInStock = $this->isProductInStock($item);
        }

        if (!$isInStock) {
            return false;
        }

        return true;
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|string
     * @throws NoSuchEntityException
     */
    private function getUrl(ProductInterface $product)
    {
        $store = $this->storeManager->getStore($product->getStoreId());
        return substr($product->getProductUrl(true), strlen($store->getBaseUrl()));
    }

    /**
     * Return an array of product parent ids
     * @param ProductInterface $product
     * @return array
     */
    private function getGroupId(ProductInterface $product)
    {
        return $this->productDataProvider->getParentProductIds([$product->getId()]);
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|string
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
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
