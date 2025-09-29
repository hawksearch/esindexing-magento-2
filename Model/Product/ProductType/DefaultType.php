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

use HawkSearch\Connector\Compatibility\PublicMethodDeprecationTrait;
use HawkSearch\EsIndexing\Helper\PricingHelper;
use HawkSearch\EsIndexing\Model\Config\Products\PriceConfig as PriceConfig;
use HawkSearch\EsIndexing\Model\Product\PriceManagementInterface;
use HawkSearch\EsIndexing\Model\Product\ProductTypeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @api
 * @since 0.8.0
 *
 * @phpstan-import-type PriceData from PriceManagementInterface
 */
abstract class DefaultType implements ProductTypeInterface
{
    use PublicMethodDeprecationTrait;

    private array $deprecatedMethods = [
        'addPricesIncludingTax' => [
            'since' => '0.8.0',
            'description' => 'Method will be removed. Handle taxes in UI.'
        ],
        'addFormattedPrices' => [
            'since' => '0.8.0',
            'description' => 'We do not send formatted prices to the index anymore. Handle price formatting in UI.'
        ],
        'handleTax' => [
            'since' => '0.8.0',
            'description' => 'Method will be removed. Handle taxes in UI.'
        ],

    ];

    private PriceCurrencyInterface $priceCurrency;
    private GroupSourceInterface $customerGroupSource;
    private GroupManagementInterface $groupManagement;
    private ModuleManager $moduleManager;
    private PricingHelper $pricingHelper;
    private PriceConfig $priceConfig;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        GroupSourceInterface $customerGroupSource,
        GroupManagementInterface $groupManagement,
        ModuleManager $moduleManager,
        PricingHelper $pricingHelper,
        PriceConfig $priceConfig = null
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->customerGroupSource = $customerGroupSource;
        $this->groupManagement = $groupManagement;
        $this->moduleManager = $moduleManager;
        $this->pricingHelper = $pricingHelper;
        $this->priceConfig = $priceConfig ?? ObjectManager::getInstance()->get(PriceConfig::class);
    }

    /**
     * Provide pricing data based on Magento "Product Price" index
     *
     * 'price_regular' - regular product price entered in admin panel
     * 'price_final' - minimum price from regular price, special price and tier price
     * 'price_min' - minimum available price. Mainly used by complex products and is calculated based on prices of
     *    children options
     * 'price_max' - maximum available price. Mainly used by complex products and is calculated based on prices of
     *     children options
     * 'price_filtered' - an aggregated single field optimal for filtering and sorting results. Can be calculated based
     *     on different price fields depending on the product type. It is used to create Price Facet and Sorting Option.
     *
     * @param ProductModel $product
     */
    public function getPriceData(ProductInterface $product): array
    {
        /**
         * ************
         * * EXAMPLES *
         * ************
         *
         * [Simple Setup]
         * [Virtual Setup]
         *
         * regular price - 6$
         * special price - 4$
         * Display price: 4$ (Regular: 6$)
         * Hawk Facet Price: 4
         *
         * $priceData['price_regular'] = 6;
         * $priceData['price_final'] = 4; // MIN(regular, special, tier)
         * $priceData['price_min'] = 4; // always = price_final
         * $priceData['price_max'] = 4; // always = price_final
         * **************************************************************
         *
         * [Downloadable Setup]
         *
         * regular price - 6$
         * special price - 5$
         * Downloadable links:
         *   - link1 - 3$
         *   - link2 - 7$
         * Display price: 5$ (Regular: 6$)
         * Hawk Facet Price: 5
         *
         * $priceData['price_regular'] = 6;
         * $priceData['price_final'] = 5; // MIN(regular, special, tier)
         * $priceData['price_min'] = 8; // price_final + MIN(link price) =  5 + 3
         * $priceData['price_max'] = 15; // price_final + SUM(links prices) = 5 + (3 + 7)
         * **************************************************************
         *
         * [Gift Card Setup] (fixed amount)
         *
         * amount - 50$
         * Display price: 50$
         * Hawk Facet Price: 50
         *
         * $priceData['price_regular'] = null;
         * $priceData['price_final'] = 50;
         * $priceData['price_min'] = 50;
         * $priceData['price_max'] = null;
         * **************************************************************
         *
         * [Gift Card Setup] (open amount)
         *
         * amount  - 100$
         * open amount from - 25$
         * open amount to - 50$
         * Display price: From 25$
         * Hawk Facet Price: 25
         *
         * $priceData['price_regular'] = null;
         * $priceData['price_final'] = 25;
         * $priceData['price_min'] = 25;
         * $priceData['price_max'] = null;
         * *************************************************************
         *
         * [Bundle Setup] (Fixed Price)
         *
         * regular price - 100$
         * special price - 90$ (10%)
         * Options:
         *   - Option1:
         *      - value1 (fixed) - 0$
         *      - value2 (percent) - 50%
         *   - Option2:
         *      - value1 (fixed) - 10$
         *   - Option3:
         *      - value1 (fixed) - 1$
         *   - Option4:
         *      - value1 (fixed) - 5$
         * Display price: From 104.4$ (Regular: 116$) To 149.4$ (Regular: 166$)
         * Hawk Facet Price: 104.4
         *
         * $priceData['price_regular'] = 100;
         * $priceData['price_final'] = 90;
         * $priceData['price_min'] = 104.4; // price_final + ( (MIN(Option1) + ... + MIN(Option4)) - 10% ) =
         * // = 90 + ((0 + 10 + 1 + 5) - 10%) = 90 + (16 - 10%) = 104.4
         * $priceData['price_max'] = 149.4; // price_final + ( (MAX(Option1) + ... + MAX(Option4)) - 10% ) =
         * // = 90 + (((100 - 50%) + 10 + 1 + 5) - 10%) = 90 + ((50 + 10 + 1 + 5) - 10%) = 90 + (66 - 10%) = 149.4
         * ************************************************************
         *
         * [Bundle Setup] (Dynamic Price)
         *
         * Options:
         *   - Option1:
         *     - Product1 - 23$
         *     - Product2 - 27$
         *     - Product3 - 32$ (special_price - 20$)
         *   - Option2:
         *     - value1 - 5$
         *   - Option1:
         *     - Product1 - 14$
         *     - Product2 - 17$
         *     - Product3 - 21$
         *   - Option4:
         *     - Product3 - 19$
         * Display price: From 58$ (Regular: 61$) To 72$ (Regular: 77$)
         * Hawk Facet Price: 58
         *
         * $priceData['price_regular'] = 0;
         * $priceData['price_final'] = 0;
         * $priceData['price_min'] = 58; // (MIN(Option1) + ... + MIN(Option4) = 20 + 5 + 14 + 19 = 58
         * $priceData['price_max'] = 72; // (MAX(Option1) + ... + MAX(Option4) = 27 + 5 + 21 + 19 = 72
         * ***********************************************************
         *
         * [Grouped Setup]
         *
         * Grouped products:
         *   - Product1 - 17$
         *   - Product2 - 14$ (special_price - 10$)
         *   - Product3 - 21$ (special_price - 15$)
         * Display price: Starting At 10$
         * Hawk Facet Price: 10
         *
         * $priceData['price_regular'] = null;
         * $priceData['price_final'] = null; // based on discount
         * $priceData['price_min'] = 10; // MIN(Grouped products)
         * $priceData['price_max'] = 17; // MAX(Grouped products)
         * **********************************************************
         *
         * [Configurable Setup 1]
         *
         * Configurations:
         *   - Product1 - 70$
         *   - Product2 - 69$
         *   - Product3 - 70$ (special_price - 60$)
         * Display price: As low as 60$
         * Hawk Facet Price: 60
         *
         * $priceData['price_regular'] = 0; // any price is possible, no relation to children
         * $priceData['price_final'] = 0; // any price is possible, no relation to children MIN(regular, special, tier)
         * $priceData['price_min'] = 60; // MIN(Configurations)
         * $priceData['price_max'] = 70; // MAX(Configurations)
         * *********************************************************
         *
         * [Configurable Setup 2]
         *
         * Configurations:
         *   - Product1 - 70$
         *   - Product2 - 70$
         *   - Product3 - 70$
         * Display price: 70$
         * Hawk Facet Price: 70
         *
         * $priceData['price_regular'] = 70; // any price is possible, no relation to children
         * $priceData['price_final'] = 70; // any price is possible, no relation to children MIN(regular, special, tier)
         * $priceData['price_min'] = 70; // MIN(Configurations)
         * $priceData['price_max'] = 70; // MAX(Configurations)
         */

        $priceData = [];
        $priceData['price_regular'] = $this->getPriceRegular($product);
        $priceData['price_final'] = $this->getPriceFinal($product);
        $priceData['price_min'] = $this->getPriceMin($product);
        $priceData['price_max'] = $this->getPriceMax($product);
        $priceData['price_filtered'] = $priceData['price_final'];

        // Add customer group prices
        if ($this->priceConfig->isIndexCustomerGroupPrices()) {
            $this->addPricesFromArray('price_group', $this->getCustomerGroupPrices($product), $priceData);
        }

        $this->roundPrices($priceData);

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
     * @param ProductModel $product
     */
    protected function getPriceRegular(ProductInterface $product): float
    {
        return (float)$product->getData(ProductInterface::PRICE);
    }

    /**
     * @param ProductModel $product
     */
    protected function getPriceFinal(ProductInterface $product): float
    {
        return (float)$product->getData('final_price');
    }

    /**
     * @param ProductModel $product
     */
    protected function getPriceMin(ProductInterface $product): float
    {
        return (float)$product->getMinimalPrice();
    }

    /**
     * @param ProductModel $product
     */
    protected function getPriceMax(ProductInterface $product): float
    {
        return max((float)$product->getData('max_price'), $this->getPriceMin($product));
    }

    public function getChildProducts(ProductInterface $product): array
    {
        return [];
    }

    /**
     * @param string $priceName
     * @param array<array-key, float|null> $prices
     * @param PriceData $priceData
     * @return void
     */
    protected function addPricesFromArray(string $priceName, array $prices, array &$priceData)
    {
        foreach ($prices as $key => $price) {
            if (null === $price) {
                continue;
            }
            $this->addSuffixedValue($priceName, (string)$key, $price, $priceData);
        }
    }

    /**
     * @param ProductModel $product
     * @param PriceData $priceData
     * @return void
     * @deprecated 0.8.0 Method will be removed. Handle taxes in UI
     */
    private function addPricesIncludingTax(ProductInterface $product, array &$priceData)
    {
        return;
    }

    /**
     * @param PriceData $priceData
     * @return void
     * @todo Review if we need to push prices rounded to the index
     */
    protected function roundPrices(array &$priceData)
    {
        foreach ($priceData as $key => $price) {
            $priceData[$key] = $this->priceCurrency->round($price);
        }
    }

    /**
     * @param ProductModel $product
     * @param PriceData $priceData
     * @return void
     * @deprecated 0.8.0 We do not send formatted prices to the index anymore. Handle price formatting in UI.
     */
    private function addFormattedPrices(ProductInterface $product, array &$priceData)
    {
        return;
    }

    /**
     * @param string $priceName
     * @param string $suffix
     * @param float $price
     * @param PriceData $priceData
     * @return void
     */
    protected function addSuffixedValue(string $priceName, string $suffix, float $price, array &$priceData)
    {
        $priceData = array_merge($priceData, $this->getSuffixedPrice($priceName, $suffix, $price));
    }

    /**
     * @return array<string, float>
     */
    private function getSuffixedPrice(string $priceName, string $suffix, float $price): array
    {
        return [$priceName . '_' . $suffix => $price];
    }

    /**
     * @param ProductModel $product
     * @param float $price
     * @param bool $forceIncludeTax
     * @deprecated 0.8.0 Method will be removed. Handle taxes in UI
     */
    private function handleTax(ProductInterface $product, float $price, bool $forceIncludeTax = false): float
    {
        return $price;
    }

    /**
     * @return list<array{"label":\Stringable, "value": int}>
     */
    protected function getCustomerGroups(): array
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        $resultGroups = $groups = $this->customerGroupSource->toOptionArray();
        if ($this->moduleManager->isEnabled('Magento_SharedCatalog')) {
            /** @see \Magento\SharedCatalog\Plugin\Source\CustomerGroupPlugin */
            $firstElement = current($groups);
            if ($firstElement !== false && isset($firstElement['value']) && is_array($firstElement['value'])) {
                $resultGroups = $firstElement['value'];
                $sharedCatalogs = next($groups);
                if ($sharedCatalogs !== false && isset($sharedCatalogs['value']) && is_array($sharedCatalogs['value'])) {
                    $resultGroups = array_merge($resultGroups, $sharedCatalogs['value']);
                }
            }
        }

        array_walk($resultGroups, function (array &$group) {
            $group['value'] = (int)$group['value'];
        });
        return $resultGroups;
    }

    /**
     * @param ProductModel $product
     * @return array<int, float>
     */
    protected function getCustomerGroupPrices(ProductInterface $product): array
    {
        $productCopy = clone $product;

        $groupPrices = [];
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = $group['value'];

            $productCopy->setData('customer_group_id', $groupId);
            $productCopy->setData('website_id', $productCopy->getStore()->getWebsiteId());
            $productCopy->unsetData('tier_price');
            $productCopy->unsetData('calculated_final_price');

            $groupPrices[$groupId] = $this->getPriceFinal($productCopy);
        }
        unset($productCopy);

        return $groupPrices;
    }

    /**
     * @throws LocalizedException
     */
    protected function getAllCustomerGroupsId(): ?int
    {
        // ex: 32000
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * @param ProductModel $product
     * @return array<int, float>
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
                $pricesByGroup[(int)$productTierPrice->getCustomerGroupId()][] = (float)$productTierPrice->getValue();
            }
        }

        foreach ($pricesByGroup as $groupId => $prices) {
            $pricesByGroup[$groupId] = min($prices);
        }

        $allGroupsId = $this->getAllCustomerGroupsId();
        $groupTierPrices = [];
        $allGroupsPrice = $pricesByGroup[$allGroupsId] ?? null;
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = $group['value'];
            $groupPrice = $pricesByGroup[$groupId] ?? $allGroupsPrice;

            if ($groupPrice !== null) {
                $groupTierPrices[$groupId] = $this->handleTax($product, $groupPrice);
            }
        }

        $product->setData('tier_price', $originalTierPrice);

        return $groupTierPrices;
    }
}
