<?php
/**
 * Copyright (c) 2023 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Config\Backend\Serialized\Processor;

use HawkSearch\Connector\Api\Data\HawkSearchFieldInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\Connector\Gateway\InstructionException;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class ProductAttributes implements ValueProcessorInterface
{
    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var ManagerInterface
     */
    private $message;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        InstructionManagerPool $instructionManagerPool,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ManagerInterface $message,
        LoggerInterface $logger
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->message = $message;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        $resultSave = [];
        $newFields = [];
        foreach ($value as $id => $item) {
            if ($id === '__empty') {
                continue;
            }

            //new value condition
            if ($item[ValueProcessorInterface::COLUMN_FIELD] === ValueProcessorInterface::SELECT_OPTION_NEW_FILED_VALUE
                && isset($item[ValueProcessorInterface::COLUMN_FIELD_NEW])) {
                $newFields[$id] = $item;
            } else {
                $resultSave[$id] = $item;
            }
        }

        $errors = [];
        foreach ($newFields as $id => $newField) {
            if (empty($newField[ValueProcessorInterface::COLUMN_FIELD_NEW]) || empty($newField[ValueProcessorInterface::COLUMN_ATTRIBUTE])) {
                continue;
            }

            try {
                $createdField = $this->addNewFiled($newField);
                $resultSave[$id] = $newField;
                $resultSave[$id][ValueProcessorInterface::COLUMN_FIELD] = $createdField->getName();
            } catch (InstructionException|NotFoundException $e) {
                $this->logger->error($e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        foreach ($errors as $error) {
            $this->message->addWarningMessage($error);
        }

        //remove values from the result which have empty COLUMN_FIELD or COLUMN_ATTRIBUTE
        return array_filter($resultSave, function ($value) {
            return !empty($value[ValueProcessorInterface::COLUMN_FIELD]) && !empty($value[ValueProcessorInterface::COLUMN_ATTRIBUTE]);
        });
    }

    /**
     * @param array $field
     * @return HawkSearchFieldInterface
     * @throws InstructionException
     * @throws NotFoundException
     */
    protected function addNewFiled($field)
    {
        $data = [
            HawkSearchFieldInterface::LABEL => $field[ValueProcessorInterface::COLUMN_FIELD_NEW] ?? '',
            HawkSearchFieldInterface::NAME => $field[ValueProcessorInterface::COLUMN_FIELD_NEW] ?? '',
        ];
        if (!empty($field[ValueProcessorInterface::COLUMN_ATTRIBUTE])) {
            try {
                $attribute = $this->productAttributeRepository
                    ->get($field[ValueProcessorInterface::COLUMN_ATTRIBUTE]);

                $data[HawkSearchFieldInterface::IS_SORT] = (bool)$attribute->getUsedForSortBy();
                $data[HawkSearchFieldInterface::IS_COMPARE] = (bool)$attribute->getIsComparable();
                $data[HawkSearchFieldInterface::IS_QUERY] = (bool)$attribute->getIsSearchable();
            } catch (NoSuchEntityException $e) {
                // not a product attribute but a system field
                $data[HawkSearchFieldInterface::IS_SORT] = false;
                $data[HawkSearchFieldInterface::IS_COMPARE] = false;
                $data[HawkSearchFieldInterface::IS_QUERY] = false;
            }
        }

        /** @var HawkSearchFieldInterface $newHawkField */
        $newHawkField = $this->instructionManagerPool
            ->get('hawksearch')->executeByCode('postField', $data)->get();

        if (!$newHawkField->getFieldId()) {
            throw new NotFoundException(
                __(
                    'Field "' . $data[HawkSearchFieldInterface::NAME] . '" hasn\'t been created.'
                )
            );
        }

        return $newHawkField;
    }

    /**
     * Cleanup values before saving
     *
     * @param array $value
     * @return array
     */
    protected function cleanupValue(array $value)
    {
        $result = [];
        foreach ($value as $inner) {
            unset($inner[ValueProcessorInterface::COLUMN_FIELD_NEW]);
            if (empty($inner[ValueProcessorInterface::COLUMN_FIELD]) || empty($inner[ValueProcessorInterface::COLUMN_ATTRIBUTE])){
                continue;
            }
            $result[] = $inner;
        }
        return $result;
    }
}
