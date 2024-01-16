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
use HawkSearch\EsIndexing\Api\Data\FacetInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use Magento\Framework\Api\DataObjectHelper;

class FacetResult implements ResultInterface
{
    /**
     * @var array
     */
    private array $result;

    /**
     * @var FacetInterfaceFactory
     */
    private FacetInterfaceFactory $facetFactory;

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var HawkSearchDataObjectHelper
     */
    private HawkSearchDataObjectHelper $hawksearchDataObjectHelper;

    /**
     * @var HttpResponseReader
     */
    private HttpResponseReader $httpResponseReader;

    /**
     * @param FacetInterfaceFactory $facetFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HawkSearchDataObjectHelper $hawksearchDataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param array $result
     */
    public function __construct(
        FacetInterfaceFactory $facetFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    ) {
        $this->result = $result;
        $this->facetFactory = $facetFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns facet result interpretation
     *
     * @return FacetInterface
     */
    public function get(): FacetInterface
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        if ($response instanceof FacetInterface) {
            return $response;
        }

        $dataObject = $this->facetFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($response),
            FacetInterface::class
        );
        return $dataObject;
    }
}
