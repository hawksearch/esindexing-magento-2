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

use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\Bulk\OperationInterface as BulkOperationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

class IndexingOperationValidator
{

    /**
     * @var BulkOperationManagement
     */
    private $bulkOperationManagement;

    /**
     * IndexingOperationValidator constructor.
     *
     * @param BulkOperationManagement $bulkOperationManagement
     */
    public function __construct(
        BulkOperationManagement $bulkOperationManagement
    ) {
        $this->bulkOperationManagement = $bulkOperationManagement;
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     */
    public function isOperationTopicAllowed(OperationInterface $operation)
    {
        return strpos($operation->getTopicName(), BulkOperationManagement::OPERATION_TOPIC_PREFIX) === 0;
    }

    /**
     * @param OperationInterface $operation
     * @return bool|void
     * @throws NoSuchEntityException
     */
    public function isPrevOperationComplete(OperationInterface $operation)
    {
        $prevOperationKey = (int)$operation->getId() === 0 ? null : (int)$operation->getId() - 1;
        $prevOperationStatus = $this->bulkOperationManagement->getOperationByBulkAndKey(
            $operation->getBulkUuid(),
            $prevOperationKey
        )->getStatus();

        return $prevOperationKey === null || $prevOperationStatus == BulkOperationInterface::STATUS_TYPE_COMPLETE;
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function isValidOperation(OperationInterface $operation)
    {
        if ($this->isOperationComplete($operation)) {
            throw new NotFoundException(
                __(
                    'Operation was already processed. Bulk UUID: %1, key: %2',
                    $operation->getBulkUuid(),
                    $operation->getId()
                )
            );
        }

        return $this->isBulkConsistent($operation->getBulkUuid());
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     */
    public function isOperationComplete(OperationInterface $operation)
    {
        return $operation->getStatus() == BulkOperationInterface::STATUS_TYPE_COMPLETE;
    }

    /**
     * @param OperationInterface $operation
     * @return bool
     */
    public function isAllBulkOperationsComplete(OperationInterface $operation)
    {
        $allCount = $this->bulkOperationManagement->getOperationsByBulkUuid($operation->getBulkUuid())->getTotalCount();
        $completeCount = $this->bulkOperationManagement->getOperationsByBulkUuidAndStatus(
            $operation->getBulkUuid(),
            BulkOperationInterface::STATUS_TYPE_COMPLETE
        )->getTotalCount();

        return $allCount == $completeCount;
    }

    /**
     * @param string $bulkUuid
     * @return bool
     * @todo Check bulk consistency: operations order is correct,
     * @todo number of operation is eq to bulk operations count,
     * @todo all bulk operations are hawksearch operations
     */
    protected function isBulkConsistent($bulkUuid)
    {
        return true;
    }
}
