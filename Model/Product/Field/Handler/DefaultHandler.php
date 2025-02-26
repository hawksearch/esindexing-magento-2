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
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * @phpstan-type ItemType ProductModel
 * @implements FieldHandlerInterface<ItemType>
 */
class DefaultHandler implements FieldHandlerInterface
{
    private ProductAttributesProvider $productAttributes;

    public function __construct(
        ProductAttributesProvider $productAttributes = null
    ) {
        $this->productAttributes = $productAttributes ?: ObjectManager::getInstance()->get(ProductAttributesProvider::class);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $attributeCode = $this->getAttributeCodeByFieldName($fieldName);

        /** @var ProductResource $productResource */
        $productResource = $item->getResource();

        /** @var AttributeResource $attributeResource */
        $attributeResource = $productResource->getAttribute($attributeCode);
        // @phpstan-ignore-next-line
        if ($attributeResource) {
            $value = $this->getProductAttributeText($item, $attributeResource);
        } else {
            $value = $item->getData($attributeCode);
        }

        return $value;
    }

    /**
     * @return string
     */
    private function getAttributeCodeByFieldName(string $fieldName): string
    {
        return $this->productAttributes->getFieldToAttributeMap()[$fieldName] ?? '';
    }

    /**
     * @param ItemType $product
     * @param AttributeResource $attribute
     * @return mixed
     */
    private function getProductAttributeText(ProductModel $product, AttributeResource $attribute)
    {
        $value = $product->getData($attribute->getAttributeCode());
        $valueText = null;

        if (!is_array($value)) {
            if ($value === null && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                $valueText = $value;
            } elseif ($attribute->getFrontendInput() == 'multiselect') {
                $valueText = $product->getAttributeText($attribute->getAttributeCode());
            } elseif ($attribute->usesSource()) {
                $valueText = $attribute->getFrontend()->getValue($product);
                if ($valueText === false) {
                    $valueText = null;
                }
            } else {
                $valueText = $value;
            }
        }

        if (is_object($valueText) && method_exists($valueText, '__toString')) {
            $valueText = $valueText->__toString();
        } elseif (is_object($valueText)) {
            $valueText = null;
        }

        return $valueText;
    }
}
