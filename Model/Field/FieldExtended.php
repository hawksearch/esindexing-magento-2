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

namespace HawkSearch\EsIndexing\Model\Field;

use HawkSearch\EsIndexing\Api\Data\FieldInterface;

class FieldExtended implements FieldExtendedInterface
{

    /**
     * @var FieldInterface
     */
    private FieldInterface $field;

    public function __construct(FieldInterface $field)
    {
        $this->field = $field;
    }


    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->field->getIsQuery()
            && in_array(
                $this->field->getFieldType(),
                [
                    FieldInterface::FIELD_TYPE_FACET,
                    FieldInterface::FIELD_TYPE_KEYWORD,
                    FieldInterface::FIELD_TYPE_TEXT,
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function isFilterable(): bool
    {
        return $this->field->getFieldType() === FieldInterface::FIELD_TYPE_FACET;
    }

    /**
     * @inheritDoc
     */
    public function isSortable(): bool
    {
        return $this->field->getIsSort();
    }

    /**
     * @inheritDoc
     */
    public function getField(): FieldInterface
    {
        return $this->field;
    }
}
