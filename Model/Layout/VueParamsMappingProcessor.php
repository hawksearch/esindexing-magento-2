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

namespace HawkSearch\EsIndexing\Model\Layout;

use Magento\Search\Helper\Data as SearchHelper;

class VueParamsMappingProcessor implements LayoutConfigProcessorInterface
{
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    /**
     * VueParamsMappingProcessor constructor.
     * @param SearchHelper $searchHelper
     */
    public function __construct(
        SearchHelper $searchHelper
    )
    {
        $this->searchHelper = $searchHelper;
    }

    /**
     * @inheritDoc
     */
    public function process($jsConfig)
    {
        $jsConfig = $jsConfig ?? [];
        $jsConfig['paramsMapping'] = [
            'keyword' => $this->searchHelper->getQueryParamName()
        ];

        return $jsConfig;
    }
}
