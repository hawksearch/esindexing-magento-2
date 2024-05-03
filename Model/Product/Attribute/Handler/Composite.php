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

namespace HawkSearch\EsIndexing\Model\Product\Attribute\Handler;

use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use HawkSearch\EsIndexing\Model\Indexing\Entity\Product\ItemsDataProvider;
use HawkSearch\EsIndexing\Model\Product\Attribute\ValueProcessorInterface;
use HawkSearch\EsIndexing\Model\Product\ProductTypePoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class Composite extends \HawkSearch\EsIndexing\Model\Indexing\AttributeHandler\Composite
{
    /**
     * @var array
     */
    private array $childrenCache = [];

    /**
     * @var ProductTypePoolInterface
     */
    private ProductTypePoolInterface $productTypePool;

    /**
     * @var ValueProcessorInterface
     */
    private ValueProcessorInterface $valueProcessor;

    /**
     * @var ItemsDataProvider
     */
    private ItemsDataProvider $childrenItemsDataProvider;

    /**
     * AttributeHandlerComposite constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProductTypePoolInterface $productTypePool
     * @param ValueProcessorInterface $valueProcessor
     * @param AttributeHandlerInterface[] $handlers
     * @param ItemsDataProvider|null $childrenItemsDataProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductTypePoolInterface $productTypePool,
        ValueProcessorInterface $valueProcessor,
        array $handlers = [],
        ItemsDataProvider $childrenItemsDataProvider = null
    ) {
        parent::__construct($objectManager, $handlers);
        $this->productTypePool = $productTypePool;
        $this->valueProcessor = $valueProcessor;
        $this->childrenItemsDataProvider = $childrenItemsDataProvider ?: ObjectManager::getInstance()->get(ItemsDataProvider::class);
    }

    /**
     * @inheritDoc
     * @param ProductInterface $item
     * @throws LocalizedException
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $value = $this->formatValue(parent::handle($item, $attributeCode));
        $relatedValues = [];

        foreach ($this->getChildren($item) as $child) {
            $relatedValues = array_merge($relatedValues, $this->formatValue($this->handle($child, $attributeCode)));
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

    /**
     * @param ProductInterface|DataObject $item
     * @return ProductInterface[]
     */
    private function getChildren(DataObject $item): array
    {
        if (!isset($this->childrenCache[$item->getId()])) {
            $productType = $this->productTypePool->get($item->getTypeId());
            $childrenCollection = [];
            if ($children = $productType->getChildProducts($item)) {
                $childIds = [];
                foreach ($children as $child) {
                    $childIds[] = $child->getId();
                }
                $childrenCollection = $this->childrenItemsDataProvider->getItems($item->getStoreId(), $childIds);
            }

            $this->childrenCache[$item->getId()] = $childrenCollection;
        }

        return $this->childrenCache[$item->getId()];
    }
}
