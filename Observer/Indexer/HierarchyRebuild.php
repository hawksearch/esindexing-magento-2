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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Observer\Indexer;

use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class HierarchyRebuild implements ObserverInterface
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * HierarchyRebuild constructor.
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        IndexManagementInterface $indexManagement
    ) {
        $this->indexManagement = $indexManagement;
    }

    /**
     * After hierarchy data is upserted the rebuild API request should follow after that
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $store = $observer->getData('store');
        $indexer = $observer->getData('indexer');
        $transport = $observer->getData('transport');

        //Get non-current index name for full reindexing process
        $indexName = $this->indexManagement->getIndexName();
        $this->indexManagement->rebuildHierarchy($indexName);
    }
}
