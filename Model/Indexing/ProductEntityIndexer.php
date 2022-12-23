<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use HawkSearch\Connector\Helper\Url as UrlHelper;
use HawkSearch\EsIndexing\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\Config\Advanced as AdvancedConfig;
use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\Config\Products as ProductsConfig;
use HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandlerInterface;
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product as CatalogProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class ProductEntityIndexer extends AbstractEntityIndexer
{
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
     * @var Product\Attributes
     */
    private $productAttributes;

    /**
     * @var Product
     */
    private $productDataProvider;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var Product\PriceManagementInterface
     */
    private $priceManagement;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var AdvancedConfig
     */
    private $advancedConfig;

    /**
     * @var AttributeHandlerInterface
     */
    private $attributeHandler;

    /**
     * @var Product\ProductTypePoolInterface
     */
    private $productTypePool;

    /**
     * ProductEntityIndexer constructor.
     * @param IndexingConfig $indexingConfig
     * @param EntityTypePoolInterface $entityTypePool
     * @param IndexManagementInterface $indexManagement
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param Visibility $visibility
     * @param Configuration $catalogInventoryConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param Json $jsonSerializer
     * @param ProductsConfig $attributesConfigProvider
     * @param Product\Attributes $productAttributes
     * @param Product $productDataProvider
     * @param ImageHelper $imageHelper
     * @param Product\PriceManagementInterface $priceManagement
     * @param UrlHelper $urlHelper
     * @param AdvancedConfig $advancedConfig
     * @param AttributeHandlerInterface $attributeHandler
     * @param Product\ProductTypePoolInterface $productTypePool
     */
    public function __construct(
        IndexingConfig $indexingConfig,
        EntityTypePoolInterface $entityTypePool,
        IndexManagementInterface $indexManagement,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        Visibility $visibility,
        Configuration $catalogInventoryConfiguration,
        StockRegistryInterface $stockRegistry,
        Json $jsonSerializer,
        ProductsConfig $attributesConfigProvider,
        Product\Attributes $productAttributes,
        Product $productDataProvider,
        ImageHelper $imageHelper,
        Product\PriceManagementInterface $priceManagement,
        UrlHelper $urlHelper,
        AdvancedConfig $advancedConfig,
        AttributeHandlerInterface $attributeHandler,
        Product\ProductTypePoolInterface $productTypePool
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

        $this->visibility = $visibility;
        $this->catalogInventoryConfiguration = $catalogInventoryConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->jsonSerializer = $jsonSerializer;
        $this->attributesConfigProvider = $attributesConfigProvider;
        $this->productAttributes = $productAttributes;
        $this->productDataProvider = $productDataProvider;
        $this->imageHelper = $imageHelper;
        $this->priceManagement = $priceManagement;
        $this->urlHelper = $urlHelper;
        $this->advancedConfig = $advancedConfig;
        $this->attributeHandler = $attributeHandler;
        $this->productTypePool = $productTypePool;
    }

    /**
     * @inheritdoc
     */
    protected function getIndexedAttributes(): array
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
     * @param ProductInterface $product
     * @return bool
     */
    private function isProductInStock(ProductInterface $product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        return $stockItem->getIsInStock();
    }

    /**
     * @param ProductInterface|CatalogProductModel|DataObject $entityItem
     * @inheritdoc
     */
    protected function getEntityId(DataObject $entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @param ProductInterface|CatalogProductModel|DataObject $item
     * @inheritdoc
     */
    protected function canItemBeIndexed(DataObject $item): bool
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
}
