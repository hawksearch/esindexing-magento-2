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

namespace HawkSearch\EsIndexing\Observer;

use HawkSearch\EsIndexing\Model\Config\Search as SearchConfig;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

class LayoutUpdateHandler implements ObserverInterface
{
    private SearchConfig $searchConfig;
    private CatalogHelper $catalogHelper;

    public function __construct(
        SearchConfig $searchConfig,
        CatalogHelper $catalogHelper
    ) {
        $this->searchConfig = $searchConfig;
        $this->catalogHelper = $catalogHelper;
    }

    public function execute(Observer $observer): void
    {
        /** @var Layout $layout */
        $layout = $observer->getData('layout');
        $action = $observer->getData('full_action_name');

        $handles = [];
        if ($this->searchConfig->isSearchEnabled() || $this->isCategoriesEnabled()) {
            $handles[] = 'hawksearch_esindexing_default_handle';
            $handles[] = 'hawksearch_esindexing_components';
        }
        $handles = array_merge($handles, $this->getResultsHandles($action));

        $layout->getUpdate()->addHandle($handles);
    }

    /**
     * @return list<string>
     */
    private function getResultsHandles(string $action): array
    {
        $allowedActions = [
            'catalogsearch_result_index',
            'catalog_category_view',
            //'hawksearch_landingPage_view'
        ];
        $isCategoryPage = $action === 'catalog_category_view';
        $handles = [];

        if (!in_array($action, $allowedActions)) {
            return $handles;
        }

        if ($isCategoryPage) {
            if (!$this->isCategoriesEnabled()) {
                return $handles;
            }
        } else {
            if (!$this->searchConfig->isSearchEnabled()) {
                return $handles;
            }
        }

        $handles[] = 'hawksearch_esindexing_results';

        return $handles;
    }

    private function isCategoriesEnabled(): bool
    {
        //@todo replace with \HawkSearch\EsIndexing\Registry\CurrentCategory::get()
        $category = $this->catalogHelper->getCategory();
        if (!$category) {
            return false;
        }

        /**
         * @todo Create a configuration setting to track if Categories are managed by Hawksearch
         */
        /*if (!<configurationValue>) {
            return false;
        }*/

        $isContentMode = $category->getDisplayMode() === Category::DM_PAGE;
        if ($isContentMode) {
            return false;
        }

        return true;
    }
}
