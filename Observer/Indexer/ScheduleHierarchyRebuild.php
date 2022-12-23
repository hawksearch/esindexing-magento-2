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

namespace HawkSearch\EsIndexing\Observer\Indexer;

use HawkSearch\EsIndexing\Model\Indexing\Entity\Type\HierarchyEntityType;
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
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var StoreInterface $store */
        $store = $observer->getData('store');
        /** @var DataObject $transport */
        $transport = $observer->getData('transport');
        /** @var HierarchyEntityType $entityType */
        $entityType = $observer->getData('entity_type');

        if (!($entityType instanceof HierarchyEntityType)) {
            return;
        }

        $dataToUpdate = (array)$transport->getData();

        $isFullReindex = true;
        $isCurrentIndex = !$isFullReindex;
        $indexName = $this->indexManagement->getIndexName($isCurrentIndex);

        $dataToUpdate[] = [
            'topic' => 'hawksearch.indexing.hierarchy.reindex',
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
