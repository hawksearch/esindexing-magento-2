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

namespace HawkSearch\EsIndexing\Model\Indexer\Entities;

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class ActionEntityDefault extends ActionAbstract
{
    private StoreManagerInterface $storeManager;
    private IndexingConfig $indexingConfig;

    public function __construct(
        ManagerInterface $eventManager,
        MessageManagerInterface $messageManager,
        BulkPublisherInterface $publisher,
        SchedulerInterface $entityScheduler,
        StoreManagerInterface $storeManager,
        IndexingConfig $indexingConfig
    ) {
        parent::__construct(
            $eventManager,
            $messageManager,
            $publisher,
            $entityScheduler
        );
        $this->storeManager = $storeManager;
        $this->indexingConfig = $indexingConfig;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function execute(array $ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        try {
            $currentStore = $this->storeManager->getStore();
            foreach ($this->storeManager->getStores() as $store) {
                if (!$this->indexingConfig->isIndexingEnabled($store->getId())) {
                    continue;
                }
                $this->storeManager->setCurrentStore($store->getId());
                $this->reindex($store, $ids);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        } finally {
            if (isset($currentStore)) {
                $this->storeManager->setCurrentStore($currentStore);
            }
        }

        return $this;
    }
}
