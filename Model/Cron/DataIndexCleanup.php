<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Cron;

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\Collection as DataIndexCollection;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\CollectionFactory as DataIndexCollectionFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;

class DataIndexCleanup
{
    private DataIndexCollectionFactory $dataIndexCollectionFactory;
    private IndexingConfig $indexingConfig;
    private StoreManagerInterface $storeManager;
    private DateTime $dateTime;
    private DateTime\DateTime $gmtDateTime;

    public function __construct(
        DataIndexCollectionFactory $dataIndexCollectionFactory,
        IndexingConfig $indexingConfig,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        DateTime\DateTime $gmtDateTime
    ) {
        $this->dataIndexCollectionFactory = $dataIndexCollectionFactory;
        $this->indexingConfig = $indexingConfig;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->gmtDateTime = $gmtDateTime;
    }

    public function execute(): void
    {
        $this->cleanupStaleIndexes();
        $this->cleanupValidIndexesRequests();
    }

    /**
     * @return void
     * @TODO add updated_at column to hawksearch_data_preload_items table first and then we can implement this method
     */
    private function cleanupValidIndexesRequests() {}

    private function cleanupStaleIndexes(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            if (!$this->indexingConfig->getStaleIndicesLifetime($store->getId())) {
                continue;
            }

            /** @var DataIndexCollection $collection */
            $collection = $this->dataIndexCollectionFactory->create();
            $connection = $collection->getConnection();

            $staleIndicesLifetime = 3600 * 24 * $this->indexingConfig->getStaleIndicesLifetime($store->getId());
            $maxUpdatedAt = $this->dateTime->formatDate((string)($this->gmtDateTime->gmtTimestamp() - $staleIndicesLifetime));
            $connection->delete(
                $collection->getResource()->getMainTable(),
                [
                    'updated_at <= ?' => $maxUpdatedAt,
                    'store_id = ?' => $store->getId(),
                ]
            );
        }
    }
}
