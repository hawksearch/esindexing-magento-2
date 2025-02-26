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

use HawkSearch\EsIndexing\Api\Data\FieldInterface;

/**
 * @internal experimental feature
 */
class AttributeFacade
{
    private AttributeAdapter $attribute;

    public function execute(FieldInterface $field, AttributeAdapter $attribute): void
    {
        $this->attribute = $attribute;

        $this->processSearchable($field)
            ->processFilterable($field)
            ->processSortable($field);
    }

    private function processSearchable(FieldInterface $field): self
    {
        $this->setFieldSearchable($field, $this->attribute->isSearchable());
        return $this;
    }

    private function processFilterable(FieldInterface $field): self
    {
        $this->setFieldFilterable($field, $this->attribute->isFilterable());
        return $this;
    }

    private function processSortable(FieldInterface $field): self
    {
        $this->setFieldSortable($field, $this->attribute->isSortable());
        return $this;
    }

    private function isFieldSearchable(FieldInterface $field): bool
    {
        return $field->getIsQuery()
            && in_array($field->getFieldType(), ['keyword', 'facet', 'text']);
    }

    private function setFieldSearchable(FieldInterface $field, bool $value): void
    {
        switch ($value) {
            case true:
                $field->setIsQuery(true);
                if (!in_array($field->getFieldType(), ['keyword', 'facet', 'text'])) {
                    $field->setFieldType('keyword');
                }

                break;
            default:
                $field->setIsQuery(false);
        }

    }

    private function isFieldFilterable(FieldInterface $field): bool
    {
        return $field->getFieldType() === FieldInterface::FIELD_TYPE_FACET;
    }

    private function setFieldFilterable(FieldInterface $field, bool $value): void
    {
        switch ($value) {
            case true:
                $field->setFieldType(FieldInterface::FIELD_TYPE_FACET);
                break;
            default:
                if ($field->getFieldType() === FieldInterface::FIELD_TYPE_FACET) {
                    $field->setFieldType(FieldInterface::FIELD_TYPE_KEYWORD);
                }
        }
    }

    private function isFieldSortable(FieldInterface $field): bool
    {
        return $field->getIsSort();
    }

    private function setFieldSortable(FieldInterface $field, bool $value): void
    {
        $field->setIsSort($value);
    }
}
