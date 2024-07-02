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

namespace HawkSearch\EsIndexing\Model\Product\Field\Handler;

use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use HawkSearch\EsIndexing\Model\Product\Attributes as ProductAttributesProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class DefaultHandler implements FieldHandlerInterface
{
    /**
     * @var array
     */
    private array $attributeValues = [];

    /**
     * @var ProductAttributesProvider
     */
    private ProductAttributesProvider $productAttributes;

    public function __construct(
        ProductAttributesProvider $productAttributes = null
    ) {
        $this->productAttributes = $productAttributes ?: ObjectManager::getInstance()->get(ProductAttributesProvider::class);
    }

    /**
     * @inheritDoc
     * @param ProductInterface $item
     * @throws LocalizedException
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $attributeCode = $this->getAttributeCodeByFieldName($fieldName);

        /** @var ProductResource $productResource */
        $productResource = $item->getResource();

        /** @var AttributeResource $attributeResource */
        $attributeResource = $productResource->getAttribute($attributeCode);
        if ($attributeResource) {
            $value = $this->getProductAttributeText($item, $attributeResource);
        } else {
            $value = $item->getData($attributeCode);
        }

        return $value;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getAttributeCodeByFieldName(string $fieldName): string
    {
        return $this->productAttributes->getFieldToAttributeMap()[$fieldName] ?? '';
    }

    /**
     * @param ProductInterface|Product $product
     * @param AttributeResource $attribute
     * @return mixed
     * @throws LocalizedException
     */
    private function getProductAttributeText(ProductInterface $product, AttributeResource $attribute)
    {
        $value = $product->getData($attribute->getAttributeCode());

        if ($value !== null) {
            if (!is_array($value)) {
                $attributeValues = $this->getAttributeOptionValues($attribute, $product->getStoreId());
                if ($attributeValues) {
                    //work on multiselect values
                    $valueIds = explode(',', (string)$value);
                    $oldValue = $value;
                    $value = [];
                    foreach ($valueIds as $id) {
                        if (!isset($attributeValues[$id])) {
                            continue;
                        }
                        $value[] = $attributeValues[$id];
                    }
                    $value = count($value) ? $value : $oldValue;
                }

                if (!is_scalar($value) && !is_array($value)) {
                    $value = (string)$value;
                }

                //last resort
                if ($value === false) {
                    $value = $attribute->getFrontend()->getValue($product);
                }
            }
        }

        return $value;
    }

    /**
     * @param AttributeResource $attribute
     * @param $storeId
     * @return array
     * @throws LocalizedException
     */
    private function getAttributeOptionValues(AttributeResource $attribute, $storeId): array
    {
        if (!isset($this->attributeValues[$attribute->getAttributeCode()][$storeId])) {
            $values = [];
            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions();
                foreach ($options as $option) {
                    if (isset($option['value'])) {
                        $values[$option['value']] = $option['label'] ?? $option['value'];
                    }
                }
            }

            $values = array_filter(array_filter($values), 'trim');
            $this->attributeValues[$attribute->getAttributeCode()][$storeId] = $values;
        }

        return $this->attributeValues[$attribute->getAttributeCode()][$storeId];
    }
}
