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

namespace HawkSearch\EsIndexing\Model\LandingPage\Field\Handler;

use HawkSearch\EsIndexing\Model\Indexing\EntityType\ProductEntityType;
use HawkSearch\EsIndexing\Model\Indexing\EntityType\ProductEntityTypeFactory;
use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;

/**
 * @implements FieldHandlerInterface<Category>
 */
class CustomSortList implements FieldHandlerInterface
{
    private ProductEntityTypeFactory $productEntityTypeFactory;

    public function __construct(ProductEntityTypeFactory $productEntityTypeFactory)
    {
        $this->productEntityTypeFactory = $productEntityTypeFactory;
    }

    /**
     * @return string
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $positionsHash = $item->getProductsPosition();
        $productIds = array_keys($positionsHash);
        $positions = array_values($positionsHash);
        asort($positions, SORT_NUMERIC);
        $productIds = array_replace($positions, $productIds);

        /** @var ProductEntityType $productEntityType */
        $productEntityType = $this->productEntityTypeFactory->create();
        $productIds = array_map(
            function ($v) use ($productEntityType) {
                return $productEntityType->getUniqueId((string)$v);
            },
            $productIds
        );

        return implode(',', $productIds);
    }
}
