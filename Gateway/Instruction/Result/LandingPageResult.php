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
use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class LandingPageResult implements ResultInterface
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var LandingPageInterfaceFactory
     */
    private $landingPageFactory;

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
     * @param LandingPageInterfaceFactory $landingPageFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HawkSearchDataObjectHelper $hawksearchDataObjectHelper
     * @param HttpResponseReader $httpResponseReader
     * @param array $result
     */
    public function __construct(
        LandingPageInterfaceFactory $landingPageFactory,
        DataObjectHelper $dataObjectHelper,
        HawkSearchDataObjectHelper $hawksearchDataObjectHelper,
        HttpResponseReader $httpResponseReader,
        array $result = []
    ) {
        $this->result = $result;
        $this->landingPageFactory = $landingPageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->hawksearchDataObjectHelper = $hawksearchDataObjectHelper;
        $this->httpResponseReader = $httpResponseReader;
    }

    /**
     * Returns result interpretation
     *
     * @return LandingPageInterface[]
     */
    public function get()
    {
        $response = $this->httpResponseReader->readResponseData($this->result);
        if (!is_array($response)) {
            return [];
        }

        $result = [];
        foreach ($response as $landingPage) {
            $dataObject = $this->landingPageFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataObject,
                $this->hawksearchDataObjectHelper->convertArrayToSnakeCase($landingPage),
                LandingPageInterface::class
            );
            $result[] = $dataObject;
        }

        return $result;
    }
}
