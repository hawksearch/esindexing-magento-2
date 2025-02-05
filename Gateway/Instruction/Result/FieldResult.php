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
use HawkSearch\Connector\Gateway\Http\ClientInterface;
use HawkSearch\Connector\Gateway\Instruction\ResultInterface;
use HawkSearch\Connector\Helper\DataObjectHelper as HawkSearchDataObjectHelper;
use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\Data\FieldInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * @phpstan-import-type HttpResult from ClientInterface
 */
class FieldResult implements ResultInterface
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
     * @param FieldInterfaceFactory $fieldFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HawkSearchDataObjectHelper $hawksearchDataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param HttpResult $result
     */
    public function __construct(
        FieldInterfaceFactory $fieldFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    ) {
        $this->result = $result;
        $this->fieldFactory = $fieldFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns result interpretation
     */
    public function get(): FieldInterface
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        if ($response instanceof FieldInterface) {
            return $response;
        }

        $dataObject = $this->fieldFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($response),
            FieldInterface::class
        );
        return $dataObject;
    }
}
