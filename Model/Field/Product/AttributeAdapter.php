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

namespace HawkSearch\EsIndexing\Model\Field\Product;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\DataObject;

class AttributeAdapter
{
    /**
     * @var ProductAttributeInterface
     */
    private ProductAttributeInterface $attribute;

    /**
     * @var string
     */
    private string $attributeCode;

    /**
     * @param ProductAttributeInterface $attribute
     * @param string $attributeCode
     */
    public function __construct(
        ProductAttributeInterface $attribute,
        string $attributeCode
    ) {
        $this->attribute = $attribute;
        $this->attributeCode = $attributeCode;
    }

    /**
     * Get product attribute instance.
     *
     * @return ProductAttributeInterface|DataObject
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Get product attribute code.
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * Check if attribute is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return !!$this->getAttribute()->getIsSearchable();
    }

    /**
     * Check if attribute is filterable.
     *
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->getAttribute()->getIsFilterable() || $this->getAttribute()->getIsFilterableInSearch();
    }

    /**
     * Check if attribute is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return !!$this->getAttribute()->getUsedForSortBy();
    }
}
