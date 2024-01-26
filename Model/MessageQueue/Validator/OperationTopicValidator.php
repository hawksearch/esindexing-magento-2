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
use HawkSearch\EsIndexing\Model\MessageQueue\Exception\InvalidBulkOperationException;
use Magento\Framework\Bulk\OperationInterface;

class OperationTopicValidator implements OperationValidatorInterface
{
    const ERROR_CODE = 10111;

    /**
     * @inheritDoc
     * @throws InvalidBulkOperationException
     */
    public function validate(OperationInterface $operation): bool
    {
        $isValid = strpos($operation->getTopicName(), BulkOperationManagement::OPERATION_TOPIC_PREFIX) === 0;
        if (!$isValid) {
            throw new InvalidBulkOperationException(__(
                'Topic %1 is not allowed for operation key %2, bulk UUID %3',
                $operation->getTopicName(),
                $operation->getId(),
                $operation->getBulkUuid()
            ), null, self::ERROR_CODE);
        }

        return $isValid;
    }
}
