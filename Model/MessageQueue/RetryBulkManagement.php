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

use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\MessageQueue\BulkPublisherInterface;

class RetryBulkManagement implements BulkManagementInterface
{
    private OperationManagementInterface $operationManagement;
    private BulkPublisherInterface $publisher;

    public function __construct(
        OperationManagementInterface $operationManagement,
        BulkPublisherInterface $publisher
    ) {
        $this->operationManagement = $operationManagement;
        $this->publisher = $publisher;
    }

    /**
     * It is used to reschedule bulk operations
     *
     * @return bool
     */
    public function scheduleBulk($bulkUuid, array $operations, $description, $userId = null)
    {
        if (!$operations) {
            return false;
        }

        foreach ($operations as $operation) {
            $this->operationManagement->changeOperationStatus(
                $operation->getBulkUuid(),
                $operation->getId(),
                OperationInterface::STATUS_TYPE_OPEN,
                null,
                null,
                $operation->getSerializedData()
            );
        }

        $this->publishOperations($operations);

        return true;
    }


    /**
     * @return bool
     * @throws \LogicException
     */
    public function deleteBulk($bulkId)
    {
        throw new \LogicException('Method is not supported.');
    }

    /**
     * Publish list of operations to the corresponding message queues.
     *
     * @param OperationInterface[] $operations
     * @return void
     */
    private function publishOperations(array $operations): void
    {
        $operationsByTopics = [];
        foreach ($operations as $operation) {
            $operationsByTopics[$operation->getTopicName()][] = $operation;
        }
        foreach ($operationsByTopics as $topicName => $operations) {
            $this->publisher->publish($topicName, $operations);
        }
    }
}
