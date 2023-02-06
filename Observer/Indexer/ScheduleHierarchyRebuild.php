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

namespace HawkSearch\EsIndexing\Observer\Indexer;

use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityType\HierarchyEntityType;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;

class ScheduleHierarchyRebuild implements ObserverInterface
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var MessageTopicResolverInterface
     */
    private $messageTopicResolver;

    /**
     * HierarchyRebuild constructor.
     *
     * @param IndexManagementInterface $indexManagement
     * @param MessageTopicResolverInterface $messageTopicResolver
     */
    public function __construct(
        IndexManagementInterface $indexManagement,
        MessageTopicResolverInterface $messageTopicResolver
    ) {
        $this->indexManagement = $indexManagement;
        $this->messageTopicResolver = $messageTopicResolver;
    }

    /**
     * After hierarchy data is upserted the rebuild API request should follow after that
     *
     * @inheritDoc
     * @param Observer $observer
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        /** @var MessageManagerInterface $messageManager */
        $messageManager = $observer->getData('message_manager');
        /** @var HierarchyEntityType $entityType */
        $entityType = $observer->getData('entity_type');
        $isFullReindex = $observer->getData('full_reindex');

        if (!($entityType instanceof HierarchyEntityType)) {
            return;
        }

        $isCurrentIndex = !$isFullReindex;
        $indexName = $this->indexManagement->getIndexName($isCurrentIndex);

        $messageManager->addMessage(
            $this->messageTopicResolver->resolve($entityType),
            [
                'class' => HierarchyManagementInterface::class,
                'method' => 'rebuildHierarchy',
                'method_arguments' => [
                    'indexName' => $indexName,
                ],
                'full_reindex' => $isFullReindex,
            ]
        );
    }
}
