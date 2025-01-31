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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPoolInterface;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\FieldManagementInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @api
 * @since 0.8.0
 */
class FieldManagement implements FieldManagementInterface
{
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;

    /**
     * FieldsManagement constructor.
     *
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool
    )
    {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @deprecated
     * @see self::getFields()
     */
    public function getHawkSearchFields(): array
    {
        return $this->getFields();
    }

    public function getFields(): array
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getFields')->get();
    }

    public function addField(FieldInterface $field): FieldInterface
    {
        /** @var FieldInterface $returnedField */
        $returnedField = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addField', $this->collectFieldData($field))->get();

        if (!$returnedField->getFieldId()) {
            throw new CouldNotSaveException(
                __('Could not save field %1', $field->getName())
            );
        }

        return $returnedField;
    }

    public function updateField(FieldInterface $field): FieldInterface
    {
        /** @var FieldInterface $returnedField */
        $returnedField = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('updateField', $this->collectFieldData($field))->get();

        if (!$returnedField->getFieldId()) {
            throw new CouldNotSaveException(
                __('Could not save field %1', $field->getName())
            );
        }

        return $returnedField;
    }

    private function collectFieldData(FieldInterface $field): array
    {
        if ($field instanceof AbstractSimpleObject) {
            return $field->__toArray();
        } else {
            throw new \InvalidArgumentException(
                __(
                    'Argument %1 passed to %2 should be an instance of %3 but %4 is given',
                    '$field',
                    __METHOD__,
                    AbstractSimpleObject::class,
                    get_class($field)
                )->render()
            );
        }
    }
}
