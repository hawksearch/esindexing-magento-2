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

namespace HawkSearch\EsIndexing\Helper;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Config as TaxConfig;

/**
 * @api
 * @since 0.8.0
 */
class PricingHelper extends AbstractHelper
{
    /**
     * @var TaxHelper
     */
    private TaxHelper $taxHelper;

    /**
     * @var CatalogHelper
     */
    private CatalogHelper $catalogHelper;

    public function __construct(
        TaxHelper $taxHelper,
        CatalogHelper $catalogHelper,
        Context $context
    )
    {
        parent::__construct($context);
        $this->taxHelper = $taxHelper;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * @return TaxHelper
     */
    public function getTaxHelper()
    {
        return $this->taxHelper;
    }

    /**
     * @param string|int|Store $store
     * @param bool $force
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    public function isIncludeTax($store, bool $force = false): bool
    {
        return $force || $this->taxHelper->getPriceDisplayType($store) == TaxConfig::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * @return float
     */
    public function handleTax(ProductModel $product, float $price, bool $forceIncludeTax = false): float
    {
        $store = $product->getStore();
        $includingTax = $this->isIncludeTax($store, $forceIncludeTax);

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
}
