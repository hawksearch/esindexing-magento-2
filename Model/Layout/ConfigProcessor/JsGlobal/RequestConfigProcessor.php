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

namespace HawkSearch\EsIndexing\Model\Layout\ConfigProcessor\JsGlobal;

use HawkSearch\EsIndexing\Model\Layout\LayoutConfigProcessorInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Search\Helper\Data as SearchHelper;

class RequestConfigProcessor implements LayoutConfigProcessorInterface
{
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var FormKey
     */
    private FormKey $formKey;

    /**
     * VueParamsMappingProcessor constructor.
     * @param SearchHelper $searchHelper
     */
    public function __construct(
        SearchHelper $searchHelper,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        FormKey $formKey
    )
    {
        $this->searchHelper = $searchHelper;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->formKey = $formKey;
    }

    /**
     * @inheritDoc
     */
    public function process($jsConfig)
    {
        $jsConfig = $jsConfig ?? [];
        $jsConfig['request'] = [
            'query' => $this->request->getParam($this->searchHelper->getQueryParamName()),
            'url' => $this->urlBuilder->getUrl('*/*/*', [
                '_use_rewrite' => true,
                '_secure' => $this->request->isSecure()
            ]),
            'formKey' => $this->formKey->getFormKey(),
            'urlEncodedParam' => ActionInterface::PARAM_NAME_URL_ENCODED
        ];

        return $jsConfig;
    }
}
