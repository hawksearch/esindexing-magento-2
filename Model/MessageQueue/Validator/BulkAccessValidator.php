<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\MessageQueue\Validator;

use HawkSearch\EsIndexing\Model\MessageQueue\Exception\InvalidBulkOperationException;
use Magento\AsynchronousOperations\Model\BulkOperationsStatus;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class BulkAccessValidator
{
    private OperationValidatorInterface $operationTopicValidator;
    private BulkOperationsStatus $bulkOperationsStatus;

    public function __construct(
        OperationValidatorInterface $operationTopicValidator,
        BulkOperationsStatus $bulkOperationsStatus
    )
    {
        $this->operationTopicValidator = $operationTopicValidator;
        $this->bulkOperationsStatus = $bulkOperationsStatus;
    }

    /**
     * Check if content is allowed
     *
     * @return bool
     */
    public function isAllowed(string $bulkUuid): bool
    {
        $isAllowed = true;
        try {
            $bulk = $this->bulkOperationsStatus->getBulkDetailedStatus($bulkUuid);
            foreach ($bulk->getOperationsList() as $operation) {
                $isAllowed = $isAllowed && $this->isTopicAllowed($operation);
            }
        } catch (NoSuchEntityException $e) {
            $isAllowed = false;
        }

        return $isAllowed;
    }

    /**
     * @return bool
     */
    private function isTopicAllowed(OperationInterface $operation): bool
    {
        $isAllowed = true;
        try {
            $this->operationTopicValidator->validate($operation);
        } catch (InvalidBulkOperationException $e) {
            $isAllowed = false;
        }

        return $isAllowed;
    }
}
