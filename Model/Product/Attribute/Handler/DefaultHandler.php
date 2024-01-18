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

namespace HawkSearch\EsIndexing\Model\Product\Attribute\Handler;

use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class DefaultHandler implements AttributeHandlerInterface
{

    /**
     * @inheritDoc
     * @param ProductInterface $item
     * @throws LocalizedException
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $value = '';

        /** @var ProductResource $productResource */
        $productResource = $item->getResource();

        /** @var AttributeResource $attributeResource */
        $attributeResource = $productResource->getAttribute($attributeCode);
        if ($attributeResource) {
            $attributeResource->setData('store_id', $item->getStoreId());

            $value = $item->getData($attributeCode);

            if ($value !== null) {
                if (!is_array($value) && $attributeResource->usesSource()) {
                    $value = $item->getAttributeText($attributeCode);
                    if (!is_scalar($value) && !is_array($value)) {
                        $value = (string)$value;
                    }
                }

                if ($value === false) {
                    $value = $attributeResource->getFrontend()->getValue($item);
                }
            }
        }

        return $value;
    }
}
