<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Product\Price\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

abstract class CompositeType extends DefaultType
{
    /**
     * Retrieve child products
     *
     * @return Product[]
     */
    abstract protected function getChildProducts(Product $product);

    /**
     * Get minimal and maximal prices for composite products
     * @param Product $product
     * @return float[]|array [min, max]
     */
    protected function getMinMaxPrice(Product $product)
    {
        $min      = PHP_INT_MAX;
        $max      = 0;

        foreach ($this->getChildProducts($product) as $subProduct) {
            $price     = $this->handleTax($product, (float)$subProduct->getFinalPrice());
            $min = min($min, $price);
            $max = max($max, $price);
        }

        return [(float)$min, (float)$max];
    }

    /**
     * @inheritdoc
     */
    protected function getCustomerGroupPrices($product)
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

        foreach ($groupPrices as $groupId => $price) {
            $groupPrices[$groupId] = min($price);
        }

        return $groupPrices;
    }

    /**
     * @inheritDoc
     */
    public function getPriceData(ProductInterface $product): array
    {
        $result = parent::getPriceData($product);
        [$minPrice, $maxPrice] = $this->getMinMaxPrice($product);
        $result['price_min'] = $minPrice;
        $result['price_max'] = $maxPrice;

        return $result;
    }

}
