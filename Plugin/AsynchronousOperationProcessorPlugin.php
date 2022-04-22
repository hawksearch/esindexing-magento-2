<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Plugin;

use HawkSearch\EsIndexing\Model\MessageQueue\IndexingOperationValidator;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Model\ConfigInterface as AsyncConfig;
use Magento\AsynchronousOperations\Model\OperationProcessor;
use Magento\Framework\Bulk\OperationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\MessageEncoder;

class AsynchronousOperationProcessorPlugin
{
    /**
     * @var MessageEncoder
     */
    private $messageEncoder;

    /**
     * @var OperationManagementInterface
     */
    private $operationManagement;

    /**
     * @var IndexingOperationValidator
     */
    private $indexingOperationValidator;

    /**
     * AsynchronousOperationProcessorPlugin constructor.
     * @param MessageEncoder $messageEncoder
     * @param OperationManagementInterface $operationManagement
     * @param IndexingOperationValidator $indexingOperationValidator
     */
    public function __construct(
        MessageEncoder $messageEncoder,
        OperationManagementInterface $operationManagement,
        IndexingOperationValidator $indexingOperationValidator
    )
    {
        $this->messageEncoder = $messageEncoder;
        $this->operationManagement = $operationManagement;
        $this->indexingOperationValidator = $indexingOperationValidator;
    }

    /**
     * @param OperationProcessor $subject
     * @param string $encodedMessage
     * @return array
     * @throws LocalizedException
     */
    public function beforeProcess(OperationProcessor $subject, string $encodedMessage)
    {
        /** @var OperationInterface $operation */
        $operation = $this->messageEncoder->decode(AsyncConfig::SYSTEM_TOPIC_NAME, $encodedMessage);

        if ($this->indexingOperationValidator->isOperationTopicAllowed($operation)) {
            try {
                if (!$this->indexingOperationValidator->isValidOperation($operation)) {
                    throw new LocalizedException(
                        __(
                            'Can\'t process operation Bulk UUID: %1, key: %2',
                            $operation->getBulkUuid(),
                            $operation->getId()
                        )
                    );
                }
            } catch (LocalizedException | NoSuchEntityException $e) {
                //@TODO Add error code mapping
                $errorCode = 100;
                $serializedData = (isset($errorCode)) ? $operation->getSerializedData() : null;
                $this->operationManagement->changeOperationStatus(
                    $operation->getBulkUuid(),
                    $operation->getId(),
                    OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                    $errorCode,
                    implode('; ', array_merge([], ...[[$e->getMessage()]])),
                    $serializedData
                );

                //re-throw exception
                throw $e;
            }
        }

        return null;
    }
}
