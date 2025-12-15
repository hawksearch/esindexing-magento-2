<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Config\Products;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PriceConfig
{
    public const XML_PATH_INDEX_PRICES = 'hawksearch_product_settings/price/index_prices';
    public const XML_PATH_INDEX_CUSTOMER_GROUP_PRICES = 'hawksearch_product_settings/price/index_customer_group_prices';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    private function getValue(string $path, ?int $scopeId = null, string $scope = ScopeInterface::SCOPE_STORES): mixed
    {
        return $this->scopeConfig->getValue($path, $scope, $scopeId);
    }

    public function isIndexPrices(?int $store = null): bool
    {
        return !!$this->getValue(self::XML_PATH_INDEX_PRICES, $store);
    }

    public function isIndexCustomerGroupPrices(?int $store = null): bool
    {
        return $this->isIndexPrices($store) && $this->getValue(self::XML_PATH_INDEX_CUSTOMER_GROUP_PRICES, $store);
    }
}
