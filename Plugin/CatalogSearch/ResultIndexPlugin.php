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

namespace HawkSearch\EsIndexing\Plugin\CatalogSearch;

use HawkSearch\EsIndexing\Model\Config\Search as SearchConfig;
use Magento\CatalogSearch\Controller\Result\Index;
use Magento\CatalogSearch\Helper\Data as CatalogSearchHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ViewInterface;
use Magento\Search\Model\QueryFactory;

class ResultIndexPlugin
{
    private QueryFactory $queryFactory;
    private ViewInterface $view;
    private SearchConfig $searchConfig;

    public function __construct(
        QueryFactory $queryFactory,
        ViewInterface $view,
        SearchConfig $searchConfig
    )
    {
        $this->queryFactory = $queryFactory;
        $this->view = $view;
        $this->searchConfig = $searchConfig;
    }

    /**
     * @param Index $subject
     * @param null $result
     */
    public function afterExecute(Index $subject, $result): void
    {
        if (!$this->searchConfig->isSearchEnabled()) {
            return;
        }

        $catalogSearchHelper = ObjectManager::getInstance()->get(CatalogSearchHelper::class);

        $query = $this->queryFactory->get();
        if ($query->getQueryText() == '') {
            if ($subject->getResponse()->getHeader('Location')) {
                $subject->getResponse()->clearHeader('Location');
                $subject->getResponse()->setHttpResponseCode(200);

                $catalogSearchHelper->checkNotes();

                $this->view->loadLayout();
                $subject->getResponse()->setNoCacheHeaders();
                $this->view->renderLayout();
            }
        }
    }
}
