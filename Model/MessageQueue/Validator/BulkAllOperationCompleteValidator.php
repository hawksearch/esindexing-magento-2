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

use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use Magento\Framework\Bulk\OperationInterface;

class BulkAllOperationCompleteValidator implements OperationValidatorInterface
{
    private BulkOperationManagement $bulkOperationManagement;

    public function __construct(
        BulkOperationManagement $bulkOperationManagement
    )
    {
        $this->bulkOperationManagement = $bulkOperationManagement;
    }

    /**
     * @return bool
     */
    public function validate(OperationInterface $operation): bool
    {
        $allCount = $this->bulkOperationManagement->getOperationsByBulkUuid($operation->getBulkUuid())->getTotalCount();
        $completeCount = $this->bulkOperationManagement->getOperationsByBulkUuidAndStatus(
            $operation->getBulkUuid(),
            OperationInterface::STATUS_TYPE_COMPLETE
        )->getTotalCount();

        return $allCount == $completeCount;
    }
}
