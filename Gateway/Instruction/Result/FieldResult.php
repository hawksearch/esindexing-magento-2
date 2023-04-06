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

namespace HawkSearch\EsIndexing\Gateway\Instruction\Result;

use HawkSearch\Connector\Gateway\Helper\HttpResponseReader;
use HawkSearch\Connector\Gateway\Instruction\ResultInterface;
use HawkSearch\Connector\Helper\DataObjectHelper as HawkSearchDataObjectHelper;
use HawkSearch\Connector\Api\Data\HawkSearchFieldInterfaceFactory;
use HawkSearch\Connector\Api\Data\HawkSearchFieldInterface;
use Magento\Framework\Api\DataObjectHelper;

class FieldResult implements ResultInterface
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var HawkSearchFieldInterfaceFactory
     */
    private $fieldFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var HawkSearchDataObjectHelper
     */
    private $hawksearchDataObjectHelper;

    /**
     * @var HttpResponseReader
     */
    private $httpResponseReader;

    /**
     * @param HawkSearchFieldInterfaceFactory $fieldInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HawkSearchDataObjectHelper $hawksearchDataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param array $result
     */
    public function __construct(
        HawkSearchFieldInterfaceFactory $fieldInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    ) {
        $this->result = $result;
        $this->fieldFactory = $fieldInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns result interpretation
     *
     * @return HawkSearchFieldInterface
     */
    public function get()
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        if ($response instanceof HawkSearchFieldInterface) {
            return $response;
        }

        $dataObject = $this->fieldFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($response),
            HawkSearchFieldInterface::class
        );
        return $dataObject;
    }
}
