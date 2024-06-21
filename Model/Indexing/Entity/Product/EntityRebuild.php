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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Product;

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as CatalogProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class EntityRebuild extends AbstractEntityRebuild
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
     * @var Product
     */
    private $productDataProvider;

    /**
     * ProductEntity constructor.
     *
     * @param EntityTypePoolInterface $entityTypePool
     * @param EventManagerInterface $eventManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param ContextInterface $indexingContext
     * @param ObjectHelper $objectHelper
     * @param Visibility $visibility
     * @param Configuration $catalogInventoryConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param Product $productDataProvider
     */
    public function __construct(
        EntityTypePoolInterface $entityTypePool,
        EventManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        ObjectHelper $objectHelper,
        Visibility $visibility,
        Configuration $catalogInventoryConfiguration,
        StockRegistryInterface $stockRegistry,
        Product\Attributes $productAttributes,
        Product $productDataProvider
    ) {
        parent::__construct(
            $entityTypePool,
            $eventManager,
            $loggerFactory,
            $storeManager,
            $indexingContext,
            $objectHelper
        );

        $this->visibility = $visibility;
        $this->catalogInventoryConfiguration = $catalogInventoryConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->productDataProvider = $productDataProvider;
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
    protected function isAllowedItem(DataObject $item): bool
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
