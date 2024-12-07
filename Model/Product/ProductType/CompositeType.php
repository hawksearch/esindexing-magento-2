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
use Magento\Catalog\Model\Product;

/**
 * @api
 * @since 0.8.0
 */
abstract class CompositeType extends DefaultType
{
    /**
     * @inheritDoc
     */
    public function getChildProducts(ProductInterface $product): array
    {
        return $product->hasData('child_products') ? $product->getData('child_products') : [];
    }
    /**
     * Get minimal and maximal prices for composite products
     * @param Product|ProductInterface $product
     * @return float[]|array [min, max]
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
     * @inheritdoc
     */
    protected function getCustomerGroupPrices(ProductInterface $product): array
    {
        $groupPrices = [];
        foreach ($this->getCustomerGroups() as $group) {
            $groupId = (string)$group['value'];
            $groupPrices[$groupId] = [];
        }

        $childGroupPrices = [];
        foreach ($this->getChildProducts($product) as $subProduct) {

            $childGroupPrices[$subProduct->getId()] = parent::getCustomerGroupPrices($subProduct);
            $childGroupPrices[$subProduct->getId()] = array_filter(
                $childGroupPrices[$subProduct->getId()],
                function($v) {
                    return !(null === $v || '' === $v);
                }
            );

            foreach ($childGroupPrices[$subProduct->getId()] as $groupId => $price) {
                if (isset($groupPrices[$groupId])) {
                    $groupPrices[$groupId][] = $price;
                }
            }
        }

        foreach ($groupPrices as $groupId => $price) {
            $groupPrices[$groupId] = !empty($price) ? min($price) : null;
        }

        return $groupPrices;
    }

    /**
     * @inheritDoc
     */
    public function getPriceData(ProductInterface $product): array
    {
        $result = parent::getPriceData($product);
        $result['price_min'] = $this->handleTax($product, $this->getPriceMin($product));
        $result['price_max'] = $this->handleTax($product, $this->getPriceMax($product));

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function getPriceRegular(ProductInterface $product): float
    {
        $basePrice = parent::getPriceRegular($product);
        return $basePrice ?: max($this->getPriceMin($product), 0);
    }

    /**
     * @inheritDoc
     */
    protected function getPriceFinal(ProductInterface $product): float
    {
        $basePrice = parent::getPriceFinal($product);
        return $basePrice ?: $this->getPriceRegular($product);
    }

}
