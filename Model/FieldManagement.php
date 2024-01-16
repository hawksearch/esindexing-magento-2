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

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\FieldManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class FieldManagement implements FieldManagementInterface
{
    /**
     * @var InstructionManagerPool
     */
    private InstructionManagerPool $instructionManagerPool;

    /**
     * FieldsManagement constructor.
     *
     * @param InstructionManagerPool $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPool $instructionManagerPool
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @inheritDoc
     * @deprecated
     * @see self::getFields()
     */
    public function getHawkSearchFields(): array
    {
        return $this->getFields();
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getFields')->get();
    }

    /**
     * @inheritDoc
     */
    public function addField(FieldInterface $field): FieldInterface
    {
        /** @var FieldInterface $returnedField */
        $returnedField = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addField', $field->__toArray())->get();

        if (!$returnedField->getFieldId()) {
            throw new CouldNotSaveException(
                __('Could not save field %1', $field->getName())
            );
        }

        return $returnedField;
    }

    /**
     * @inheritDoc
     */
    public function updateField(FieldInterface $field): FieldInterface
    {
        /** @var FieldInterface $returnedField */
        $returnedField = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('updateField', $field->__toArray())->get();

        if (!$returnedField->getFieldId()) {
            throw new CouldNotSaveException(
                __('Could not save field %1', $field->getName())
            );
        }

        return $returnedField;
    }
}
