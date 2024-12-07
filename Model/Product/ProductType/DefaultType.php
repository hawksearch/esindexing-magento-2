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

namespace HawkSearch\EsIndexing\Model\Product\ProductType;

use HawkSearch\EsIndexing\Helper\PricingHelper;
use HawkSearch\EsIndexing\Model\Product\ProductTypeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @api
 * @since 0.8.0
 */
abstract class DefaultType implements ProductTypeInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * @var GroupSourceInterface
     */
    private GroupSourceInterface $customerGroupSource;

    /**
     * @var GroupManagementInterface
     */
    private GroupManagementInterface $groupManagement;

    /**
     * @var ModuleManager
     */
    private ModuleManager $moduleManager;

    /**
     * @var PricingHelper
     */
    private PricingHelper $pricingHelper;

    /**
     * NonComplexProductType constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param GroupSourceInterface $customerGroupSource
     * @param GroupManagementInterface $groupManagement
     * @param ModuleManager $moduleManager
     * @param PricingHelper $pricingHelper
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        GroupSourceInterface $customerGroupSource,
        GroupManagementInterface $groupManagement,
        ModuleManager $moduleManager,
        PricingHelper $pricingHelper
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->customerGroupSource = $customerGroupSource;
        $this->groupManagement = $groupManagement;
        $this->moduleManager = $moduleManager;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @inheritDoc
     */
    public function getPriceData(ProductInterface $product): array
    {
        $priceData = [];
        $priceData['price_regular'] = $this->getPriceRegular($product);
        $priceData['price_final'] = $this->getPriceFinal($product);

        // Add customer group prices
        $this->addPricesFromArray('price_group', $this->getCustomerGroupPrices($product), $priceData);

        //@todo tier price

        //add prices including tax
        $this->addPricesIncludingTax($product, $priceData);

        $this->roundPrices($priceData);

        //add formatted prices (this step should be the last one)
        $this->addFormattedPrices($product, $priceData);

        //$currencyList = $store->getAvailableCurrencyCodes();
        /*foreach ($currencyList as $currencyCode) {
            $priceData[$currencyCode] = [];

            $price = $this->getPriceRegular($product, $currencyCode);

            $priceData[$currencyCode]['price'] = $this->priceCurrency->round($price);
            $priceData[$currencyCode]['price_formatted'] = $this->priceCurrency->round($price);
        }*/

        return $priceData;
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @return float
     */
    protected function getPriceRegular(ProductInterface $product): float
    {
        return (float)$product->getPrice();
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @return float
     */
    protected function getPriceFinal(ProductInterface $product): float
    {
        return (float)$product->getFinalPrice();
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @return float
     */
    protected function getPriceMin(ProductInterface $product): float
    {
        return (float)$product->getMinimalPrice();
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @return float
     */
    protected function getPriceMax(ProductInterface $product): float
    {
        return max((float)$product->getMaxPrice(), $this->getPriceMin($product));
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
    protected function addPricesFromArray(string $priceName, array $prices, array &$priceData)
    {
        foreach ($prices as $key => $price) {
            if (null === $price) {
                continue;
            }
            $this->addSuffixedValue($priceName, $key, $price, $priceData);
        }
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @param $priceData
     */
    protected function addPricesIncludingTax(ProductInterface $product, &$priceData)
    {
        $priceDataCopy = $priceData;
        foreach ($priceDataCopy as $key => $price) {
            $price = $this->handleTax($product, (float)$price, true);
            $this->addSuffixedValue($key, 'include_tax', $price, $priceData);
        }
    }

    /**
     * @param array $priceData
     * @todo Review if we need to push prices rounded to the index
     */
    protected function roundPrices(array &$priceData)
    {
        foreach ($priceData as $key => $price) {
            $priceData[$key] = $this->priceCurrency->round($price);
        }
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @param array $priceData
     */
    protected function addFormattedPrices(ProductInterface $product, array &$priceData)
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
     * @param string|int|float $suffix
     * @param float|string|int $price
     * @param array $priceData
     */
    protected function addSuffixedValue (string $priceName, $suffix, $price, array &$priceData) {
        $priceData[$priceName . '_' . $suffix] = $price;
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @param float $price input product price
     * @param bool $forceIncludeTax
     * @return float
     */
    protected function handleTax(ProductInterface $product, float $price, bool $forceIncludeTax = false): float
    {
        return $this->pricingHelper->handleTax($product, $price, $forceIncludeTax);
    }

    /**
     * @return array
     */
    protected function getCustomerGroups(): array
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
     * @param ProductInterface|ProductModel $product
     * @return array
     */
    protected function getCustomerGroupPrices(ProductInterface $product): array
    {
        $productCopy = clone $product;

        $groupPrices = [];
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = (string)$group['value'];

            $productCopy->setData('customer_group_id', $groupId);
            $productCopy->setData('website_id', $productCopy->getStore()->getWebsiteId());
            $productCopy->unsetData('tier_price');
            $productCopy->unsetData('calculated_final_price');

            $groupPrices[$groupId] = $this->getPriceFinal($productCopy);

            //$groupPrices[$groupId] = $this->handleTax($product, $finalPrice);
        }
        unset($productCopy);

        return $groupPrices;
    }

    /**
     * @return int|null
     * @throws LocalizedException
     */
    protected function getAllCustomerGroupsId(): ?int
    {
        // ex: 32000
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * @param ProductInterface|ProductModel $product
     * @return array
     * @throws LocalizedException
     */
    protected function getTierPrices(ProductInterface $product): array
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
