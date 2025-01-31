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

use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract;
use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionFull;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class InitializeFullReindexing implements ObserverInterface
{
    private IndexManagementInterface $indexManagement;

    public function __construct(
        IndexManagementInterface $indexManagement
    )
    {
        $this->indexManagement = $indexManagement;
    }

    /**
     * Perform actions related to full reindexing initialization
     */
    public function execute(Observer $observer)
    {
        /** @var MessageManagerInterface $messageManager */
        $messageManager = $observer->getData('message_manager');
        /** @var ActionAbstract $indexerAction */
        $indexerAction = $observer->getData('indexer_action');

        if (!($indexerAction instanceof ActionFull)) {
            return;
        }

        $this->indexManagement->initializeFullReindex();

        $messageManager->addMessage(
            'hawksearch.indexing.fullreindex.start',
            [
                'full_reindex' => true,
            ]
        );
    }
}
