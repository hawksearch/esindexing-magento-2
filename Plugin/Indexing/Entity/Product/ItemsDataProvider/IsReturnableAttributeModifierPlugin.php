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

namespace HawkSearch\EsIndexing\Plugin\Indexing\Entity\Product\ItemsDataProvider;

use HawkSearch\EsIndexing\Model\Indexing\Entity\Product\ItemsDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as CatalogProductModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class IsReturnableAttributeModifierPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ItemsDataProvider $subject
     * @param ProductInterface|CatalogProductModel[] $result
     * @return ProductInterface|CatalogProductModel[]
     */
    public function afterGetItems(ItemsDataProvider $subject, array $result): array
    {
        if (!class_exists("\Magento\Rma\Model\Product\Source")) {
            return $result;
        }

        $useConfigValues = [
            null,
            \Magento\Rma\Model\Product\Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG,
            (string)\Magento\Rma\Model\Product\Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG
        ];

        $isEnabled = null;
        foreach ($result as $item) {
            $isEnabled = $isEnabled !== null
                ? $isEnabled
                : (string) (int) $this->scopeConfig->isSetFlag(
                \Magento\Rma\Model\Product\Source::XML_PATH_PRODUCTS_ALLOWED,
                ScopeInterface::SCOPE_STORE,
                $item->getStore()
            );

            $item->setIsReturnable(
                in_array($item->getIsReturnable(), $useConfigValues) ? $isEnabled : $item->getIsReturnable()
            );
        }
        return $result;
    }
}
