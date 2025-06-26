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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;

/**
 * @api
 * @since 0.8.0
 */
abstract class CompositeType extends DefaultType
{
    /**
     * @param ProductModel $product
     */
    public function getChildProducts(ProductInterface $product): array
    {
        return $product->hasData('child_products') ? $product->getData('child_products') : [];
    }

    /**
     * Get minimal and maximal prices for composite products
     *
     * @param ProductModel $product
     * @return non-empty-list{0: float, 1: float} 0-key min value, 1-key max value
     */
    protected function getMinMaxPrice(ProductInterface $product): array
    {
        $min = PHP_INT_MAX;
        $max = 0;

        if ($this->getChildProducts($product)) {
            foreach ($this->getChildProducts($product) as $subProduct) {
                if ($subProduct->isDisabled()) {
                    continue;
                }
                $price = $this->handleTax($product, (float)$subProduct->getFinalPrice());
                $min = min($min, $price);
                $max = max($max, $price);
            }
        }

        if ($min === PHP_INT_MAX) {
            $min = $max;
        }

        return [(float)$min, (float)$max];
    }

    /**
     * @return array<int, float>
     */
    protected function getCustomerGroupPrices(ProductInterface $product): array
    {
        $groupPrices = [];
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = $group['value'];
            $groupPrices[$groupId] = [];
        }

        $childGroupPrices = [];
        foreach ($this->getChildProducts($product) as $subProduct) {

            $childGroupPrices[$subProduct->getId()] = parent::getCustomerGroupPrices($subProduct);

            foreach ($childGroupPrices[$subProduct->getId()] as $groupId => $price) {
                if (isset($groupPrices[$groupId])) {
                    $groupPrices[$groupId][] = $price;
                }
            }
        }

        foreach ($groupPrices as $groupId => $prices) {
            $groupPrices[$groupId] = !empty($prices) ? min($prices) : null;
        }

        return array_filter($groupPrices, function (?float $v) {
            return $v !== null;
        });
    }

    public function getPriceData(ProductInterface $product): array
    {
        $priceData = parent::getPriceData($product);
        $priceData['price_filtered'] = $priceData['price_min'];

        return $priceData;
    }

    protected function getPriceRegular(ProductInterface $product): float
    {
        $basePrice = parent::getPriceRegular($product);
        return $basePrice ?: max($this->getPriceMin($product), 0);
    }

    protected function getPriceFinal(ProductInterface $product): float
    {
        $basePrice = parent::getPriceFinal($product);
        return $basePrice ?: $this->getPriceRegular($product);
    }

}
