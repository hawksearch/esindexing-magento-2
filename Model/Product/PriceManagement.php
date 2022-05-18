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

namespace HawkSearch\EsIndexing\Model\Product;

use HawkSearch\EsIndexing\Model\Product\ProductTypePoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class PriceManagement
 * @package HawkSearch\EsIndexing\Model\Product
 */
class PriceManagement implements PriceManagementInterface
{
    private const DEFAULT_PRICE_PRODUCT_TYPE = 'simple';

    /**
     * @var ProductTypePoolInterface
     */
    private $productTypePool;

    /**
     * PriceManagement constructor.
     * @param ProductTypePoolInterface $productTypePool
     */
    public function __construct(
        ProductTypePoolInterface $productTypePool
    )
    {
        $this->productTypePool = $productTypePool;
    }

    /**
     * @inheritDoc
     */
    public function collectPrices(ProductInterface $product, array &$itemData)
    {
        try {
            $priceProvider = $this->productTypePool->get($product->getTypeId());
        } catch (NotFoundException $e) {
            $priceProvider = $this->productTypePool->get(self::DEFAULT_PRICE_PRODUCT_TYPE);
        }
        $itemData = array_merge($itemData, $priceProvider->getPriceData($product));
    }
}
