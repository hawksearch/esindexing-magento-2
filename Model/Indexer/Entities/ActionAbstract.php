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

use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @api
 * @since 0.8.0
 */
abstract class ActionAbstract
{
    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var BulkPublisherInterface
     */
    protected $publisher;

    /**
     * @var SchedulerInterface
     */
    protected $entityScheduler;

    public function __construct(
        ManagerInterface $eventManager,
        MessageManagerInterface $messageManager,
        BulkPublisherInterface $publisher,
        SchedulerInterface $entityScheduler
    ) {
        $this->eventManager = $eventManager;
        $this->messageManager = $messageManager;
        $this->publisher = $publisher;
        $this->entityScheduler = $entityScheduler;
    }

    /**
     * Execute action for given ids
     *
     * @param list<int> $ids
     * @return $this
     */
    abstract public function execute(array $ids);

    /**
     * @param StoreInterface $store
     * @param list<int>|null $ids Schedule full reindexing if null
     * @return void
     */
    protected function reindex(StoreInterface $store, ?array $ids = null)
    {
        //before schedule
        $this->eventManager->dispatch(
            'hawksearch_esindexing_action_reindex_before',
            [
                'store' => $store,
                'indexer_action' => $this,
                'message_manager' => $this->messageManager
            ]
        );

        $this->entityScheduler->schedule($store, $ids);

        $this->eventManager->dispatch(
            'hawksearch_esindexing_action_reindex_after',
            [
                'store' => $store,
                'indexer_action' => $this,
                'message_manager' => $this->messageManager
            ]
        );

        $this->publisher->publish();
    }
}
