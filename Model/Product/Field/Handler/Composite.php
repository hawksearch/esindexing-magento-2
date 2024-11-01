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
use HawkSearch\EsIndexing\Model\Product\Attribute\ValueProcessorInterface;
use HawkSearch\EsIndexing\Model\Product\ProductTypePoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class Composite extends \HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite
{
    /**
     * @var ProductTypePoolInterface
     */
    private ProductTypePoolInterface $productTypePool;

    /**
     * @var ValueProcessorInterface
     */
    private ValueProcessorInterface $valueProcessor;

    /**
     * Composite constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductTypePoolInterface $productTypePool
     * @param ValueProcessorInterface $valueProcessor
     * @param FieldHandlerInterface[] $handlers
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
     * @throws LocalizedException
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $value = $this->formatValue(parent::handle($item, $fieldName));
        $relatedValues = [];

        foreach ($this->getChildren($item) as $child) {
            $relatedValues = array_merge($relatedValues, $this->formatValue($this->handle($child, $fieldName)));
        }

        /** @var ProductResource $productResource */
        $productResource = $item->getResource();
        /** @var AttributeResource $attributeResource */
        $attributeResource = $productResource->getAttribute($fieldName);

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
    private function formatValue(mixed $value)
    {
        $result = [];
        if (is_array($value)) {
            array_push($result, ...$value);
        } else {
            $result = (array)$value;
        }

        return $result;
    }

    /**
     * @param ProductInterface|DataObject $item
     * @return ProductInterface[]
     */
    private function getChildren(DataObject $item): array
    {
        $productType = $this->productTypePool->get($item->getTypeId());
        return $productType->getChildProducts($item);
    }
}
