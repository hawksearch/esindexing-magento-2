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

class ActionFull extends ActionAbstract
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var IndexingConfig
     */
    private $indexingConfig;

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
     * Execute full reindex action
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function execute(?array $ids = null)
    {
        try {
            $currentStore = $this->storeManager->getStore();
            foreach ($this->storeManager->getStores() as $store) {
                if (!$this->indexingConfig->isIndexingEnabled($store->getId())) {
                    continue;
                }
                $this->storeManager->setCurrentStore($store->getId());
                $this->reindex($store);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        } finally {
            $this->storeManager->setCurrentStore($currentStore);
        }

        return $this;
    }
}
