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
 *      'store_id'?: int|string,
 *      'index'?: string,
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

    private StoreManagerInterface $storeManager;
    private LoggerInterface $logger;
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
    ) {
        $this->storeManager = $storeManager;
        $this->logger = $loggerFactory->create();
        $this->indexManagement = $indexManagement;
        parent::__construct($data);
    }

    /**
     * @param string $topicName
     * @param MessageType $data
     * @return $this
     */
    public function addMessage(string $topicName, array $data)
    {
        $messages = (array)$this->_get(self::DATA_MESSAGES);
        $messages[] = [
            'topic' => $topicName,
            'data' => $this->updateApplicationHeaders($data),
        ];

        return $this->setMessages($messages);
    }

    /**
     * @param list<array{topic: string, data: MessageType}> $messages
     * @return $this
     */
    public function setMessages(array $messages)
    {
        return $this->setData(self::DATA_MESSAGES, $messages);
    }

    /**
     * @return list<array{topic: string, data: MessageType}>
     */
    public function getMessages()
    {
        return array_values((array)$this->_get(self::DATA_MESSAGES));
    }

    /**
     * Set current store_id in $messageData['application_headers']
     * so consumer may check store_id and execute operation in correct store scope.
     * Prevent publishing inconsistent messages because of store_id not defined or wrong.
     * Set other operation global data
     *
     * @param MessageType $messageData
     * @return MessageType
     */
    private function updateApplicationHeaders(array $messageData): array
    {
        $messageData['application_headers'] = $messageData['application_headers'] ?? [];

        try {
            $messageData['application_headers']['store_id'] = $messageData['store_id'] ?? $this->storeManager->getStore()->getId();
            unset($messageData['store_id']);

            $messageData['application_headers']['full_reindex'] = $messageData['full_reindex'] ?? false;
            unset($messageData['full_reindex']);

            $isFullReindex = $messageData['application_headers']['full_reindex'];
            $messageData['application_headers']['index'] = $messageData['index'] ?? $this->indexManagement->getIndexName(!$isFullReindex);
            unset($messageData['index']);
        } catch (NoSuchEntityException $e) {
            $errorMessage = sprintf(
                "Can't get current storeId and inject to the message queue. Error %s.",
                $e->getMessage()
            );
            $this->logger->error($errorMessage);
            throw new \LogicException($errorMessage);
        }

        return $messageData;
    }
}
