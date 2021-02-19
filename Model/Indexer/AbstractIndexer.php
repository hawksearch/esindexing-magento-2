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

namespace HawkSearch\EsIndexing\Model\Indexer;


use HawkSearch\EsIndexing\Model\Config\General;
use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\EntityIndexerPoolInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\BulkPublisherInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractIndexer
{
    /**
     * @var BulkPublisherInterface
     */
    private $publisher;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Indexing
     */
    private $indexingConfig;

    /**
     * @var General
     */
    private $generalConfig;

    /**
     * @var EntityIndexerPoolInterface
     */
    private $entityIndexerPool;

    /**
     * AbstractIndexer constructor.
     * @param BulkPublisherInterface $publisher
     * @param StoreManagerInterface $storeManager
     * @param Indexing $indexingConfig
     * @param General $generalConfig
     * @param EntityIndexerPoolInterface $entityIndexerPool
     */
    public function __construct(
        BulkPublisherInterface $publisher,
        StoreManagerInterface $storeManager,
        Indexing $indexingConfig,
        General $generalConfig,
        EntityIndexerPoolInterface $entityIndexerPool
    ) {
        $this->publisher = $publisher;
        $this->storeManager = $storeManager;
        $this->indexingConfig = $indexingConfig;
        $this->generalConfig = $generalConfig;
        $this->entityIndexerPool = $entityIndexerPool;
    }

    /**
     * @param array $ids
     * @param StoreInterface $store
     * @throws LocalizedException
     */
    protected function rebuildPartial($ids)
    {
        $stores = $this->storeManager->getStores();
        $operations = [];

        foreach ($stores as $store) {
            if (!$this->generalConfig->isIndexingEnabled($store->getId())) {
                continue;
            }

            $batchSize = $this->indexingConfig->getItemsBatchSize($store->getId());
            $chunks = array_chunk($ids, $batchSize);

            $dataToUpdate = [];
            foreach ($chunks as $chunk) {
                $dataToUpdate[] = [
                    'class' => get_class($this->entityIndexerPool->getIndexerByCode($this->getEntityIndexerCode())),
                    'method' => 'rebuildEntityIndex',
                    'method_arguments' => [
                        'storeId' => $store->getId(),
                        'entityIds' => $chunk
                    ],
                    'full_reindex' => false,
                ];
            }

            $this->publisher->publish(
                __(
                    'Update delta index for %1 items of %2 entity',
                    count($ids),
                    $this->entityIndexerPool->getIndexerByCode($this->getEntityIndexerCode())
                ),
                $dataToUpdate
            );
        }




    }

    protected function rebuildFull()
    {
        $this->publisher->publish('test', []);
    }

    abstract protected function getEntityIndexerCode();
}
