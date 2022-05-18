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

use HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandlerInterface;
use HawkSearch\EsIndexing\Model\Product\ProductTypePoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;

class Composite extends \HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandler\Composite
{
    /**
     * @var ProductTypePoolInterface
     */
    private $productTypePool;

    /**
     * AttributeHandlerComposite constructor.
     * @param ProductTypePoolInterface $productTypePool
     * @param AttributeHandlerInterface[] $handlers
     */
    public function __construct(
        ProductTypePoolInterface $productTypePool,
        array $handlers = []
    ) {
        parent::__construct($handlers);
        $this->productTypePool = $productTypePool;
    }

    /**
     * @inheritDoc
     * @param ProductInterface $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $value = parent::handle($item, $attributeCode);

        $productType = $this->productTypePool->get($item->getTypeId());
        if ($children = $productType->getChildProducts($item)) {
            $value = $this->castChildValueType($value);
            foreach ($children as $child) {
                $value = array_merge($value, $this->castChildValueType($this->handle($child, $attributeCode)));
            }

            //$value = array_unique($value);
        }

        return $value;
    }

    /**
     * Safely apply values of array type.
     * @param $value
     * @return array
     */
    private function castChildValueType($value)
    {
        $result = [];
        if (is_array($value)) {
            array_push($result, ...$value);
        } else {
            $result[] = $value;
        }

        return $result;
    }
}
