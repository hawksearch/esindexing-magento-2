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

namespace HawkSearch\EsIndexing\Model\Product\ProductType;

use HawkSearch\EsIndexing\Model\Product\ProductTypeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Config as TaxConfig;

abstract class DefaultType implements ProductTypeInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    /**
     * @var GroupSourceInterface
     */
    private $customerGroupSource;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * NonComplexProductType constructor.
     * @param PriceCurrencyInterface $priceCurrency
     * @param CatalogHelper $catalogHelper
     * @param TaxConfig $taxConfig
     * @param GroupSourceInterface $customerGroupSource
     * @param GroupManagementInterface $groupManagement
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        CatalogHelper $catalogHelper,
        TaxConfig $taxConfig,
        GroupSourceInterface $customerGroupSource,
        GroupManagementInterface $groupManagement,
        ModuleManager $moduleManager
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->catalogHelper = $catalogHelper;
        $this->taxConfig = $taxConfig;
        $this->customerGroupSource = $customerGroupSource;
        $this->groupManagement = $groupManagement;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     * @param ProductInterface|Product $product
     */
    public function getPriceData(ProductInterface $product): array
    {
        $store = $product->getStore();
        //@todo tier price

        $priceData = [];
        $priceData['price_regular'] = $this->handleTax($product, (float)$product->getPrice());
        $priceData['price_final'] = $this->handleTax($product, (float)$product->getFinalPrice());

        // Add customer group prices
        $this->addPricesFromArray('price_group', $this->getCustomerGroupPrices($product), $priceData);

        //add prices including tax
        $this->addPricesIncludingTax($product, $priceData);

        $this->roundPrices($priceData);

        //add formatted prices (this step should be the last one)
        $this->addFormattedPrices($product, $priceData);

        //$currencyList = $store->getAvailableCurrencyCodes();
        /*foreach ($currencyList as $currencyCode) {
            $priceData[$currencyCode] = [];

            $price = $this->getRegularPrice($product, $currencyCode);

            $priceData[$currencyCode]['price'] = $this->priceCurrency->round($price);
            $priceData[$currencyCode]['price_formatted'] = $this->priceCurrency->round($price);
        }*/

        return $priceData;
    }

    /**
     * @inheritDoc
     */
    public function getChildProducts(ProductInterface $product): array
    {
        return [];
    }

    /**
     * @param string $priceName
     * @param array $prices
     * @param array $priceData
     */
    protected function addPricesFromArray($priceName, array $prices, array &$priceData)
    {
        foreach ($prices as $key => $price) {
            if (null === $price) {
                continue;
            }
            $this->addSuffixedValue($priceName, $key, $price, $priceData);
        }
    }

    /**
     * @param ProductModel $product
     * @param $priceData
     */
    protected function addPricesIncludingTax($product, &$priceData)
    {
        $priceDataCopy = $priceData;
        foreach ($priceDataCopy as $key => $price) {
            $price = $this->handleTax($product, (float)$price, true);
            $this->addSuffixedValue($key, 'include_tax', $price, $priceData);
        }
    }

    /**
     * @param array $priceData
     */
    protected function roundPrices(array &$priceData)
    {
        foreach ($priceData as $key => $price) {
            $priceData[$key] = $this->priceCurrency->round($price);
        }
    }

    /**
     * @param ProductModel $product
     * @param array $priceData
     */
    protected function addFormattedPrices(ProductModel $product, array &$priceData)
    {
        $priceDataCopy = $priceData;
        $store = $product->getStore();
        foreach ($priceDataCopy as $key => $price) {
            $price = $this->priceCurrency->format(
                $price,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $store
            );
            $this->addSuffixedValue($key, 'formatted', $price, $priceData);
        }
    }

    /**
     * @param string $priceName
     * @param string $suffix
     * @param float|string|int $price
     * @param array $priceData
     */
    protected function addSuffixedValue ($priceName, $suffix, $price, array &$priceData) {
        $priceData[$priceName . '_' . $suffix] = $price;
    }

    /**
     * @param ProductModel $product
     * @param float $price inputted product price
     * @param bool $forceIncludeTax
     * @return float
     */
    protected function handleTax($product, float $price, bool $forceIncludeTax = false): float
    {
        $store = $product->getStore();
        $includingTax = $this->isTaxIncluded($store, $forceIncludeTax);

        return (float) $this->catalogHelper->getTaxPrice(
            $product,
            $price,
            $includingTax,
            null,
            null,
            null,
            $store,
            null
        );
    }

    /**
     * @param null|string|bool|int|Store $store
     * @param bool $force
     * @return bool
     */
    protected function isTaxIncluded($store, bool $force = false)
    {
        return $force || $this->taxConfig->getPriceDisplayType($store) === TaxConfig::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * @param ProductModel $product
     * @return float
     */
    protected function getRegularPrice($product, $currencyCode): float
    {
        return $this->handleTax(
            $product,
            $this->priceCurrency->convert((float)$product->getPrice(), $product->getStore(), $currencyCode)
        );
    }

    /**
     * @return array
     */
    protected function getCustomerGroups()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        $groups = $this->customerGroupSource->toOptionArray();
        $resultGroups = [];
        if ($this->moduleManager->isEnabled('Magento_SharedCatalog')) {
            $firstElement = current($groups);
            if (isset($firstElement['value']) && is_array($firstElement['value'])) {
                $resultGroups = $firstElement['value'];
            }
            $sharedCatalogs = next($groups);
            if ($sharedCatalogs !== false && isset($sharedCatalogs['value']) && is_array($sharedCatalogs['value'])) {
                $resultGroups = array_merge($resultGroups, $sharedCatalogs['value']);
            }
        }

        return $resultGroups;
    }

    /**
     * @param ProductModel $product
     * @return array
     */
    protected function getCustomerGroupPrices($product)
    {
        $productCopy = clone $product;

        $groupPrices = [];
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = (string)$group['value'];

            $productCopy->setData('customer_group_id', $groupId);
            $productCopy->setData('website_id', $productCopy->getStore()->getWebsiteId());
            $productCopy->unsetData('tier_price');

            $finalPrice = (float)$productCopy->getPriceModel()->getFinalPrice(1, $productCopy);

            $groupPrices[$groupId] = $this->handleTax($product, $finalPrice);
        }
        unset($productCopy);

        return $groupPrices;
    }

    /**
     * @return int|null
     * @throws LocalizedException
     */
    protected function getAllCustomerGroupsId()
    {
        // ex: 32000
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * @param ProductModel $product
     * @return array
     * @throws LocalizedException
     */
    protected function getTierPrices(ProductModel $product)
    {
        $originalTierPrice = $product->getData('tier_price');
        $product->unsetData('tier_price');

        $pricesByGroup = [];
        $productTierPrices = $product->getTierPrices();
        if (!is_null($productTierPrices)) {
            foreach ($productTierPrices as $productTierPrice) {
                $pricesByGroup[$productTierPrice->getCustomerGroupId()][] = $productTierPrice->getValue();
            }
        }

        foreach ($pricesByGroup as $groupId => $prices) {
            $pricesByGroup[$groupId] = min($prices);
        }

        $allGroupsId = $this->getAllCustomerGroupsId();
        $groupTierPrices = [];
        $allGroupsPrice = $productTierPrices[$allGroupsId] ?? null;
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = $group['value'];

            $groupTierPrices[$groupId] = $pricesByGroup[$groupId] ?? $allGroupsPrice;

            if ($groupTierPrices[$groupId] !== null) {
                $groupTierPrices[$groupId] = $this->handleTax($product, (float)$groupTierPrices[$groupId]);
            }
        }

        $product->setData('tier_price', $originalTierPrice);

        return $groupTierPrices;
    }
}
