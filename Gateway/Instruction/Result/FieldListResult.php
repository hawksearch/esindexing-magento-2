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

namespace HawkSearch\EsIndexing\Gateway\Instruction\Result;

use HawkSearch\Connector\Gateway\Helper\HttpResponseReader;
use HawkSearch\Connector\Gateway\Instruction\ResultInterface;
use HawkSearch\Connector\Helper\DataObjectHelper as HawkSearchDataObjectHelper;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\Data\FieldInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * @phpstan-import-type HttpResult from ResultInterface
 */
class FieldListResult implements ResultInterface
{
    /**
     * @var HttpResult
     */
    private array $result;
    private FieldInterfaceFactory $fieldFactory;
    private DataObjectHelper $dataObjectHelper;
    private HawkSearchDataObjectHelper $hawksearchDataObjectHelper;
    private HttpResponseReader $httpResponseReader;

    /**
     * @param FieldInterfaceFactory $fieldInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HawkSearchDataObjectHelper $hawksearchDataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param HttpResult $result
     */
    public function __construct(
        FieldInterfaceFactory $fieldInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    )
    {
        $this->result = $result;
        $this->fieldFactory = $fieldInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns result interpretation
     *
     * @return FieldInterface[]
     */
    public function get(): array
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        if (!is_array($response)) {
            return [];
        }

        $result = [];
        foreach ($response as $field) {
            if ($field instanceof FieldInterface) {
                $result[] = $field;
                continue;
            }

            $dataObject = $this->fieldFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataObject,
                $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($field),
                FieldInterface::class
            );
            $result[] = $dataObject;
        }

        return $result;
    }
}
