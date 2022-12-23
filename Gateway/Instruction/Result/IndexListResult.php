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

namespace HawkSearch\EsIndexing\Gateway\Instruction\Result;

use HawkSearch\Connector\Gateway\Helper\HttpResponseReader;
use HawkSearch\Connector\Gateway\Instruction\ResultInterface;
use HawkSearch\Connector\Helper\DataObjectHelper as HawkSearchDataObjectHelper;
use HawkSearch\EsIndexing\Api\Data\IndexListInterface;
use HawkSearch\EsIndexing\Api\Data\IndexListInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class IndexListResult implements ResultInterface
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var IndexListInterfaceFactory
     */
    private $indexListFactory;

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
     * @param IndexListInterfaceFactory $indexListFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param array $result
     */
    public function __construct(
        IndexListInterfaceFactory $indexListFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    ) {
        $this->result = $result;
        $this->indexListFactory = $indexListFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns result interpretation
     *
     * @return IndexListInterface
     */
    public function get()
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        $dataObject = $this->indexListFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($response),
            IndexListInterface::class
        );
        return $dataObject;
    }
}
