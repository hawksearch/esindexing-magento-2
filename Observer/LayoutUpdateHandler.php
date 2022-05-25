<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
namespace HawkSearch\EsIndexing\Observer;

use HawkSearch\EsIndexing\Model\Config\Search as SearchConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

class LayoutUpdateHandler implements ObserverInterface
{
    /**
     * @var SearchConfig
     */
    private $searchConfig;

    /**
     * LayoutUpdateHandler constructor.
     * @param SearchConfig $searchConfig
     */
    public function __construct(
        SearchConfig $searchConfig
    ) {
        $this->searchConfig = $searchConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var Layout $layout */
        $layout = $observer->getData('layout');
        $action = $observer->getData('full_action_name');

        /** @TODO Create a configuration setting to track if Categories are managed by Hawksearch */
        $isManageCategories = true;
        $handles = [];
        switch ($action) {
            case 'catalogsearch_result_index':
                if ($this->searchConfig->isSearchEnabled()) {
                    $handles[] = 'hawksearch_catalogsearch_result_index';
                }
                break;
            case 'catalog_category_view':
                if ($isManageCategories) {
                    $handles[] = 'hawksearch_catalog_category_view';
                }
                break;
            case 'hawksearch_landingPage_view':
                break;
        }

        if ($this->searchConfig->isSearchEnabled() || $isManageCategories) {
            $handles[] = 'hawksearch_esindexing_default_handle';
            $handles[] = 'hawksearch_esindexing_components';
        }

        $layout->getUpdate()->addHandle($handles);
    }
}
