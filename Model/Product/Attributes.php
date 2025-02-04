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

namespace HawkSearch\EsIndexing\Model\Product;

use HawkSearch\EsIndexing\Model\Config\Products as ProductsConfig;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * @api
 * @since 0.8.0
 */
class Attributes
{

    /**
     * @var array<string, string>
     */
    private array $attributes;
    private Config $eavConfig;
    private Json $jsonSerializer;
    private ProductsConfig $attributesConfigProvider;

    public function __construct(
        Config $eavConfig,
        Json $jsonSerializer,
        ProductsConfig $attributesConfigProvider
    ) {
        $this->eavConfig = $eavConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->attributesConfigProvider = $attributesConfigProvider;
    }

    /**
     * @return array<string, string>
     * @throws LocalizedException
     */
    public function getAllAttributes()
    {
        if (!isset($this->attributes)) {
            $productEntityAttributes = array_merge(
                $this->getMandatoryAttributes(),
                $this->eavConfig->getEntityAttributeCodes(Product::ENTITY)
            );

            $productEntityAttributes = array_diff($productEntityAttributes, $this->getExcludedAttributes());

            foreach ($productEntityAttributes as $code) {
                $this->attributes[$code] = $this->eavConfig
                    ->getAttribute(Product::ENTITY, $code)
                    ->getFrontendLabel();
            }

            ksort($this->attributes);
        }

        return $this->attributes;
    }

    /**
     * Returns list of product attributes which should be skipped by indexing processor.
     * Attributes from this list are excluded from any visual interfaces.
     *
     * @return list<string>
     */
    public function getExcludedAttributes()
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public function getMandatoryAttributes()
    {
        return array_filter(array_values($this->getMandatoryFieldMap()));
    }

    /**
     * @return array<string, string>
     */
    public function getMandatoryFieldMap()
    {
        return [
            'type_id' => 'type_id',
            'thumbnail_url' => 'thumbnail',
            'image_url' => 'image',
            'name' => 'name',
            'sku' => 'sku',
            'category' => '',
            'url' => '',
            'visibility_search' => '',
            'visibility_catalog' => '',
        ];
    }

    /**
     * Product data which is calculated
     *
     * @return list<string>
     */
    public function getExtraDataCodes()
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getIndexedAttributes()
    {
        return array_filter(array_values($this->getFieldToAttributeMap()));
    }

    /**
     * Return mapping hash.
     * Keys of the hash represent fields, values represent attributes.
     *
     * @return array<string, string>
     */
    public function getFieldToAttributeMap(): array
    {
        $currentAttributesConfig = $this->jsonSerializer->unserialize(
            $this->attributesConfigProvider->getAttributes()
        );

        $map = [];
        foreach ($currentAttributesConfig as $configItem) {
            if (isset($configItem['field'])) {
                $map[$configItem['field']] = $configItem['attribute'] ?? '';
            }
        }

        return array_merge($map, $this->getMandatoryFieldMap());
    }
}
