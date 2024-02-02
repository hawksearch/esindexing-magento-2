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

use Magento\Catalog\Api\Data\ProductInterface;

class Configurable extends CompositeType
{
    /**
     * @inheritdoc
     */
    public function getChildProducts(ProductInterface $product): array
    {
        return $product->getTypeInstance()->getUsedProducts($product);
    }

    /**
     * Avoid returning final price including tax
     * Force to load min_price from price index
     *
     * @param ProductInterface $product
     * @return float
     * @see \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price::getFinalPrice
     */
    public function getPriceFinal(ProductInterface $product): float
    {
        return max($this->getPriceMin($product), 0);
    }
}
