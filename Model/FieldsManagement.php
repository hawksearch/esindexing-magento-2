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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\Connector\Gateway\Http\ClientInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\FieldsManagementInterface;
use HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes;
use HawkSearch\EsIndexing\Model\Product\Attributes;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class FieldsManagement implements FieldsManagementInterface
{
    /**#@+
     * Constants
     */
    const FIELD_SUFFIX = '_field';
    const CONFIG_NAME = 'groups[products][fields][custom_attributes][value][<%- _id %>][attribute]';
    const OPTION_ID = '<%- _id %>_attribute';
    /**#@-*/

    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Config\Products
     */
    private $attributesConfigProvider;

    /**
     * @var Attributes
     */
    private $productAttributes;

    /**
     * FieldsManagement constructor.
     * @param InstructionManagerPool $instructionManagerPool
     * @param LoggerInterface $logger
     * @param Config\Products $attributesConfigProvider
     * @param Json $jsonSerializer
     * @param DataObjectFactory $dataObjectFactory
     * @param Product\Attributes $productAttributes
     */
    public function __construct(
        InstructionManagerPool $instructionManagerPool,
        LoggerInterface $logger,
        Config\Products $attributesConfigProvider,
        Json $jsonSerializer,
        DataObjectFactory $dataObjectFactory,
        Attributes $productAttributes
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
        $this->logger = $logger;
        $this->attributesConfigProvider = $attributesConfigProvider;
        $this->jsonSerializer = $jsonSerializer;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productAttributes = $productAttributes;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncProductAttributesConfig()
    {
        $result = [];

        try {
            $hawkFieldsResponse = $this->instructionManagerPool->get('hawksearch')
                ->executeByCode('getFields')->get();
            $currentAttributesConfig = $this->jsonSerializer->unserialize(
                $this->attributesConfigProvider->getAttributes()
            );

            if ($hawkFieldsResponse[ClientInterface::RESPONSE_CODE] === 200) {
                //prepare array fields
                $arrayFields = [];
                $allProductAttributes = $this->productAttributes->getAllAttributes();
                /** @var FieldInterface $field */
                foreach ($hawkFieldsResponse[ClientInterface::RESPONSE_DATA] as $field) {
                    if (!isset($allProductAttributes[$field->getName()])) {
                        continue;
                    }
                    $arrayFields[$field[FieldInterface::NAME] . self::FIELD_SUFFIX] = [
                        CustomAttributes::COLUMN_ATTRIBUTE => $field->getName(),
                    ];
                }

                //prepare return data
                foreach ($arrayFields as $rowId => $row) {
                    $rowColumnValues = [];
                    foreach ($row as $key => $value) {
                        $row[$key] = $value;
                        $rowColumnValues[$rowId . '_' . $key] = $row[$key];
                    }
                    $row['_id'] = $rowId;
                    $row['column_values'] = $rowColumnValues;
                    $row['option_extra_attrs']['option_' . $this->calcOptionHash(
                        $row[CustomAttributes::COLUMN_ATTRIBUTE]
                    )] = 'selected="selected"';
                    $result[$rowId] = $this->dataObjectFactory->create()->addData($row)->toJson();
                }
            } else {
                throw new InputException(__($hawkFieldsResponse[ClientInterface::RESPONSE_MESSAGE]));
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new InputException(__('Synchronization error occurred: "%1"', $e->getMessage()), $e);
        }

        return $result;
    }

    /**
     * Calculate CRC32 hash for option value
     *
     * @param string $optionValue Value of the option
     * @return string
     */
    private function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32(self::CONFIG_NAME . self::OPTION_ID . $optionValue));
    }

    /**
     * @inheritDoc
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function getHawkSearchFields()
    {
        $hawkFieldsResponse =  $this->instructionManagerPool->get('hawksearch')
            ->executeByCode('getFields')->get();

        if ($hawkFieldsResponse[ClientInterface::RESPONSE_CODE] === 200) {
            return is_array($hawkFieldsResponse[ClientInterface::RESPONSE_DATA])
                ? $hawkFieldsResponse[ClientInterface::RESPONSE_DATA]
                : [];
        } else {
            throw new InstructionException(__($hawkFieldsResponse[ClientInterface::RESPONSE_MESSAGE]));
        }
    }
}
