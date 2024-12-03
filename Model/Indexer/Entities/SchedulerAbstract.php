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

use HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @api
 * @since 0.8.0
 */
class SchedulerAbstract implements SchedulerInterface
{
    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var EntityTypeInterface
     */
    private $entityType;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var MessageTopicResolverInterface
     */
    private $messageTopicResolver;

    public function __construct(
        EventManagerInterface $eventManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EntityTypeInterface $entityType,
        MessageManagerInterface $messageManager,
        MessageTopicResolverInterface $messageTopicResolver
    ) {
        $this->eventManager = $eventManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->entityType = $entityType;
        $this->messageManager = $messageManager;
        $this->messageTopicResolver = $messageTopicResolver;
    }

    /**
     * @throws InputException
     */
    public function schedule(StoreInterface $store, ?array $ids = null)
    {
        $isFullReindex = $ids === null;

        $this->eventManager->dispatch(
            'hawksearch_esindexing_schedule_entity_items_before',
            [
                'store' => $store,
                'indexer' => $this,
                'entity_type' => $this->entityType,
                'message_manager' => $this->messageManager,
                'full_reindex' => $isFullReindex
            ]
        );

        $batchSize = $this->entityType->getConfigHelper()->getBatchSize($store->getId());
        if ($isFullReindex) {
            $items = $this->entityType->getItemsDataProvider()->getItems((int)$store->getId());
            $batches = ceil(count($items) / $batchSize);
        } else {
            $batches = ceil(count($ids) / $batchSize);
        }

        for ($page = 1; $page <= $batches; $page++) {
            if ($isFullReindex) {
                $this->searchCriteriaBuilder->setPageSize($batchSize);
                $this->searchCriteriaBuilder->setCurrentPage($page);
            } else {
                $this->searchCriteriaBuilder->addFilter(
                    'ids',
                    array_slice($ids, $page * $batchSize - $batchSize, $batchSize)
                );
            }

            $this->messageManager->addMessage(
                $this->messageTopicResolver->resolve($this->entityType),
                [
                    'class' => get_class($this->entityType->getRebuilder()),
                    'method' => 'rebuild',
                    'method_arguments' => [
                        'searchCriteria' => $this->searchCriteriaBuilder->create()->__toArray(),
                    ],
                    'full_reindex' => $isFullReindex,
                ]
            );
        }

        $this->eventManager->dispatch(
            'hawksearch_esindexing_schedule_entity_items_after',
            [
                'store' => $store,
                'indexer' => $this,
                'entity_type' => $this->entityType,
                'message_manager' => $this->messageManager,
                'full_reindex' => $isFullReindex
            ]
        );
    }
}
