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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-type MessageType array{
 *      'class'?: class-string,
 *      'method'?: callable-string,
 *      'method_arguments'?: array<array-key, mixed>,
 *      'full_reindex'?: bool,
 *      'application_headers'?: array{
 *          'store_id'?: int|string,
 *          'index'?: string,
 *          'full_reindex'?: bool,
 *      }
 *  }
 */
class MessageManager extends AbstractSimpleObject implements MessageManagerInterface
{
    public const DATA_MESSAGES = 'messages';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var IndexManagementInterface
     */
    private IndexManagementInterface $indexManagement;

    /**
     * @param StoreManagerInterface $storeManager
     * @param LoggerFactoryInterface $loggerFactory
     * @param IndexManagementInterface $indexManagement
     * @param array<self::DATA_*, mixed> $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LoggerFactoryInterface $loggerFactory,
        IndexManagementInterface $indexManagement,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->logger = $loggerFactory->create();
        $this->indexManagement = $indexManagement;
        parent::__construct($data);
    }

    public function addMessage($topicName, $data)
    {
        $messages = $this->_get(self::DATA_MESSAGES);
        $messages[] = [
            'topic' => $topicName,
            'data' => $this->updateApplicationHeaders($data),
        ];

        return $this->setMessages($messages);
    }

    public function setMessages(array $messages)
    {
        return $this->setData(self::DATA_MESSAGES, $messages);
    }

    public function getMessages()
    {
        return array_values($this->_get(self::DATA_MESSAGES));
    }

    /**
     * Set current store_id in messageData['application_headers']
     * so consumer may check store_id and execute operation in correct store scope.
     * Prevent publishing inconsistent messages because of store_id not defined or wrong.
     * Set other operation global data
     *
     * @param MessageType $data
     * @return array
     */
    private function updateApplicationHeaders(array $data)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $isFullReindex = $data['full_reindex'] ?? false;
            $indexName = $this->indexManagement->getIndexName(!$isFullReindex);
        } catch (NoSuchEntityException $e) {
            $errorMessage = sprintf(
                "Can't get current storeId and inject to the message queue. Error %s.",
                $e->getMessage()
            );
            $this->logger->error($errorMessage);
            throw new \LogicException($errorMessage);
        }

        $data['application_headers'] = $data['application_headers'] ?? [];
        $data['application_headers']['store_id'] = $storeId;
        $data['application_headers']['index'] = $indexName;
        $data['application_headers']['full_reindex'] = $isFullReindex;

        return $data;
    }
}
