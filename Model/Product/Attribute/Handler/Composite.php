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
use HawkSearch\EsIndexing\Model\Product\Attribute\ValueProcessorInterface;
use HawkSearch\EsIndexing\Model\Product\ProductTypePoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class Composite extends \HawkSearch\EsIndexing\Model\Indexing\AttributeHandler\Composite
{
    /**
     * @var ProductTypePoolInterface
     */
    private $productTypePool;

    /**
     * @var ValueProcessorInterface
     */
    private $valueProcessor;

    /**
     * AttributeHandlerComposite constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductTypePoolInterface $productTypePool
     * @param ValueProcessorInterface $valueProcessor
     * @param AttributeHandlerInterface[] $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductTypePoolInterface $productTypePool,
        ValueProcessorInterface $valueProcessor,
        array $handlers = []
    ) {
        parent::__construct($objectManager, $handlers);
        $this->productTypePool = $productTypePool;
        $this->valueProcessor = $valueProcessor;
    }

    /**
     * @inheritDoc
     * @param ProductInterface $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $value = $this->formatValue(parent::handle($item, $attributeCode));
        $relatedValues = [];

        $productType = $this->productTypePool->get($item->getTypeId());
        if ($children = $productType->getChildProducts($item)) {
            foreach ($children as $child) {
                $relatedValues = array_merge($relatedValues, $this->formatValue($this->handle($child, $attributeCode)));
            }
        }

        /** @var ProductResource $productResource */
        $productResource = $item->getResource();
        /** @var AttributeResource $attributeResource */
        $attributeResource = $productResource->getAttribute($attributeCode);

        if ($attributeResource) {
            $value = $this->valueProcessor->process($attributeResource, $value, $relatedValues);
        }

        return $value;
    }

    /**
     * Safely apply values of array type.
     *
     * @param mixed $value
     * @return array
     */
    private function formatValue($value)
    {
        $result = [];
        if (is_array($value)) {
            array_push($result, ...$value);
        } else {
            $result = (array)$value;
        }

        return $result;
    }
}
