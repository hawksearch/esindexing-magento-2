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

use Magento\AsynchronousOperations\Model\BulkOperationsStatus;
use Magento\Framework\Exception\NoSuchEntityException;

class BulkAccessValidator
{
    /**
     * @var IndexingOperationValidator
     */
    private $operationValidator;

    /**
     * @var BulkOperationsStatus
     */
    private $bulkOperationsStatus;

    /**
     * BulkAccessValidator Constructor
     *
     * @param IndexingOperationValidator $operationValidator
     * @param BulkOperationsStatus $bulkOperationsStatus
     */
    public function __construct(
        IndexingOperationValidator $operationValidator,
        BulkOperationsStatus $bulkOperationsStatus
    ) {
        $this->operationValidator = $operationValidator;
        $this->bulkOperationsStatus = $bulkOperationsStatus;
    }

    /**
     * Check if content is allowed
     *
     * @param string $bulkUuid
     * @return bool
     */
    public function isAllowed(string $bulkUuid)
    {
        $isAllowed = true;
        try {
            $bulk = $this->bulkOperationsStatus->getBulkDetailedStatus($bulkUuid);
            foreach ($bulk->getOperationsList() as $operation) {
                $isAllowed = $isAllowed && $this->operationValidator->isOperationTopicAllowed($operation);
            }
        } catch (NoSuchEntityException $e) {
            $isAllowed = false;
        }

        return $isAllowed;
    }
}
