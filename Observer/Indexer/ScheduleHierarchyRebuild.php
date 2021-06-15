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

use HawkSearch\EsIndexing\Model\Indexing\EntityIndexerPoolInterface;
use HawkSearch\EsIndexing\Model\Indexing\HierarchyEntityIndexer;
use HawkSearch\EsIndexing\Model\Indexing\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Api\Data\StoreInterface;

class ScheduleHierarchyRebuild implements ObserverInterface
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var EntityIndexerPoolInterface
     */
    private $entityIndexerPool;

    /**
     * HierarchyRebuild constructor.
     * @param IndexManagementInterface $indexManagement
     * @param EntityIndexerPoolInterface $entityIndexerPool
     */
    public function __construct(
        IndexManagementInterface $indexManagement,
        EntityIndexerPoolInterface $entityIndexerPool
    ) {
        $this->indexManagement = $indexManagement;
        $this->entityIndexerPool = $entityIndexerPool;
    }

    /**
     * After hierarchy data is upserted the rebuild API request should follow after that
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var StoreInterface $store */
        $store = $observer->getData('store');
        /** @var DataObject $transport */
        $transport = $observer->getData('transport');
        $indexerCode = $observer->getData('items_indexer_code');

        if (!($this->entityIndexerPool->getIndexerByCode($indexerCode) instanceof HierarchyEntityIndexer)) {
            return;
        }

        $dataToUpdate = (array)$transport->getData();

        $isFullReindex = true;
        $isCurrentIndex = !$isFullReindex;
        $indexName = $this->indexManagement->getIndexName($store->getId(), $isCurrentIndex);

        $dataToUpdate[] = [
            'class' => HierarchyManagementInterface::class,
            'method' => 'rebuildHierarchy',
            'method_arguments' => [
                'indexName' => $indexName,
            ],
            'full_reindex' => $isFullReindex,
        ];

        $transport->setData($dataToUpdate);
    }
}
