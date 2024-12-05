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

namespace HawkSearch\EsIndexing\Model\Config\Backend\Serialized\Processor;

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\Data\FacetInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\Data\FieldInterfaceFactory;
use HawkSearch\EsIndexing\Api\FacetManagementInterface;
use HawkSearch\EsIndexing\Api\FieldManagementInterface;
use HawkSearch\EsIndexing\Model\Field\FieldExtendedInterface;
use HawkSearch\EsIndexing\Model\Field\FieldExtendedInterfaceFactory;
use HawkSearch\EsIndexing\Model\Field\Product\AttributeFacade;
use HawkSearch\EsIndexing\Model\Field\Product\AttributeProvider;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-type ValueItemRow array{'field': string, 'attribute': string, 'field_new'?: string}
 * @phpstan-type ValueItems array<string, ValueItemRow>
 * @implements ValueProcessorInterface<ValueItems, ValueItems>
 */
class ProductAttributes implements ValueProcessorInterface
{
    /**
     * @var FieldInterface[]|null
     */
    private ?array $fieldsCache = null;

    /**
     * @var FacetInterface[]|null
     */
    private ?array $facetsCache = null;

    /**
     * @var AttributeProvider
     */
    private AttributeProvider $attributeProvider;

    /**
     * @var AttributeFacade
     */
    private AttributeFacade $attributeFacade;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $message;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var FieldInterfaceFactory
     */
    private FieldInterfaceFactory $fieldFactory;

    /**
     * @var FacetInterfaceFactory
     */
    private FacetInterfaceFactory $facetFactory;

    /**
     * @var FieldManagementInterface
     */
    private FieldManagementInterface $fieldManagement;

    /**
     * @var FacetManagementInterface
     */
    private FacetManagementInterface $facetManagement;

    /**
     * @var FieldExtendedInterfaceFactory
     */
    private FieldExtendedInterfaceFactory $fieldExtendedFactory;

    /**
     * @var SerializerInterface|null
     */
    private ?SerializerInterface $serializer;

    public function __construct(
        AttributeProvider $attributeProvider,
        AttributeFacade $attributeFacade,
        ManagerInterface $message,
        LoggerFactoryInterface $loggerFactory,
        FieldInterfaceFactory $fieldFactory,
        FacetInterfaceFactory $facetFactory,
        FieldManagementInterface $fieldManagement,
        FacetManagementInterface $facetManagement,
        FieldExtendedInterfaceFactory $fieldExtendedFactory,
        ?SerializerInterface $serializer = null
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->attributeFacade = $attributeFacade;
        $this->message = $message;
        $this->logger = $loggerFactory->create();
        $this->fieldFactory = $fieldFactory;
        $this->facetFactory = $facetFactory;
        $this->fieldManagement = $fieldManagement;
        $this->facetManagement = $facetManagement;
        $this->fieldExtendedFactory = $fieldExtendedFactory;
        $this->serializer = $serializer ?? ObjectManager::getInstance()->get(SerializerInterface::class);
    }

    public function process(array $value, ValueInterface $configValue): array
    {
        $value = $resultSave = $this->filterValue($value);
        try {
            $oldValue = (array)$this->serializer->unserialize($configValue->getOldValue());
        } catch (\InvalidArgumentException $e) {
            $oldValue = [];
        }
        $updatedRows = $this->getRowsUpdated(
            $value,
            $oldValue);
        $createdRows = $this->getRowsCreated($value);
        $resultSave = array_diff_key($resultSave, $createdRows);

        $errors = [];
        foreach ($updatedRows as $row) {
            try {
                $updatedField = $this->updateFiled($row);
                $extendedField = $this->fieldExtendedFactory->create(['field' => $updatedField]);
                $this->updateFacet($extendedField);
            } catch (CouldNotSaveException|NotFoundException $e) {
                $this->logger->error($e->getMessage());
                //@TODO keep config not updated
                $errors[] = $e->getMessage();
            }
        }

        foreach ($createdRows as $id => $row) {
            try {
                $createdField = $this->addNewFiled($row);
                $extendedField = $this->fieldExtendedFactory->create(['field' => $createdField]);
                $this->updateFacet($extendedField);
                $resultSave[$id] = $row;
                $resultSave[$id][self::COLUMN_FIELD] = $createdField->getName();
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        foreach ($errors as $error) {
            $this->message->addWarningMessage($error);
        }

        return $resultSave;
    }

    /**
     * @param ValueItems $value
     * @return ValueItems
     */
    protected function filterValue(array $value): array
    {
        $usedFields = [];
        $filterItem = function(array $v, string $k) use (&$usedFields) {
            //check item key
            $isEmptyKey = $k === '__empty';

            //check item value
            $isEmptyField = empty($v[self::COLUMN_FIELD]);
            $isEmptyNewField = (
                (isset($v[self::COLUMN_FIELD]) && $v[self::COLUMN_FIELD] === self::SELECT_OPTION_NEW_FILED_VALUE)
                && empty($v[self::COLUMN_FIELD_NEW])
            );
            $isEmptyAttribute = empty($v[self::COLUMN_ATTRIBUTE]);
            $isFieldUnique = true;
            if (!$isEmptyField) {
                $isFieldUnique = !in_array($v[self::COLUMN_FIELD], $usedFields);
                $usedFields[] = $v[self::COLUMN_FIELD];
            }

            return !$isEmptyKey && !$isEmptyField && !$isEmptyNewField && !$isEmptyAttribute && $isFieldUnique;
        };

        return array_filter($value, $filterItem, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param ValueItems $rows
     * @param ValueItems $oldConfigValue
     * @return ValueItems
     */
    protected function getRowsUpdated(array $rows, array $oldConfigValue): array
    {
        $fields = array_column($oldConfigValue, self::COLUMN_FIELD);
        $attributes = array_column($oldConfigValue, self::COLUMN_ATTRIBUTE);
        $oldConfigCombined = array_combine($fields, $attributes);

        $filterItem = function(array $v) use ($oldConfigCombined) {
            $isChangedField = !array_key_exists($v[self::COLUMN_FIELD], $oldConfigCombined)
                && $v[self::COLUMN_FIELD] !== self::SELECT_OPTION_NEW_FILED_VALUE;
            $isChangedAttribute = isset($oldConfigCombined[$v[self::COLUMN_FIELD]])
                && $v[self::COLUMN_ATTRIBUTE] !== $oldConfigCombined[$v[self::COLUMN_FIELD]];

            return $isChangedField || $isChangedAttribute;
        };

        return array_filter($rows, $filterItem);
    }

    /**
     * @param ValueItems $rows
     * @return ValueItems
     */
    protected function getRowsCreated(array $rows): array
    {
        $filterItem = function(array $v) {
            return $v[self::COLUMN_FIELD] === self::SELECT_OPTION_NEW_FILED_VALUE
                && isset($v[self::COLUMN_FIELD_NEW]);
        };

        return array_filter($rows, $filterItem);
    }

    /**
     * @param ValueItemRow $fieldData
     * @return FieldInterface
     * @throws CouldNotSaveException
     */
    protected function addNewFiled(array $fieldData): FieldInterface
    {
        /** @var FieldInterface $newField */
        $newField = $this->fieldFactory->create();
        $newField->setName($fieldData[self::COLUMN_FIELD_NEW] ?? '')
            ->setLabel($fieldData[self::COLUMN_FIELD_NEW] ?? '');

        $attribute = $this->attributeProvider->getByCode($fieldData[self::COLUMN_ATTRIBUTE]);
        $this->attributeFacade->execute($newField, $attribute);

        return $this->fieldManagement->addField($newField);
    }

    /**
     * @param ValueItemRow $fieldData
     * @return FieldInterface
     * @throws NotFoundException|CouldNotSaveException
     */
    protected function updateFiled(array $fieldData): FieldInterface
    {
        $field = $this->getFieldByName($fieldData[self::COLUMN_FIELD]);
        if (!$field->getFieldId()) {
            throw new NotFoundException(__("Can't update field. Field '%1' doesn't exist."));
        }

        $attribute = $this->attributeProvider->getByCode($fieldData[self::COLUMN_ATTRIBUTE]);
        $this->attributeFacade->execute($field, $attribute);

        return $this->fieldManagement->updateField($field);
    }

    /**
     * @return FieldInterface[]
     */
    protected function getFields(): array
    {
        if ($this->fieldsCache === null) {
            $this->fieldsCache = $this->fieldManagement->getFields();
        }

        return $this->fieldsCache;
    }

    /**
     * @return FieldInterface
     */
    protected function getFieldByName(string $name): FieldInterface
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return $this->fieldFactory->create();
    }

    /**
     * @return FacetInterface[]
     */
    protected function getFacets(): array
    {
        if ($this->facetsCache === null) {
            $this->facetsCache = $this->facetManagement->getFacets();
        }

        return $this->facetsCache;
    }

    /**
     * @return FacetInterface
     */
    protected function getFacetByField(FieldExtendedInterface $field): FacetInterface
    {
        $result = $this->facetFactory->create();
        foreach ($this->getFacets() as $facet) {
            if ($field->getField()->getName() === $facet->getField()) {
                $result = $facet;
                break;
            }
        }

        /**
         * @todo Move logic to Facade class
         */
        $result->setIsVisible($field->isFilterable());
        if (!$result->getFacetId() && $field->isFilterable()) {
            $result->setField($field->getField()->getName())
                ->setName($field->getField()->getName());
        }

        return $result;
    }

    /**
     * @return FacetInterface
     * @throws CouldNotSaveException
     */
    protected function updateFacet(FieldExtendedInterface $field): FacetInterface
    {
        $facet = $this->getFacetByField($field);

        if (!$facet->getFacetId() && $facet->getField()) {
            return $this->facetManagement->addFacet($facet);
        } elseif ($facet->getFacetId()) {
            return $this->facetManagement->updateFacet($facet);
        }

        return $facet;
    }
}
