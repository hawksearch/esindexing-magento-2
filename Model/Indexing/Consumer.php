<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexing;

use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Consumer
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * InitFullReindex constructor.
     * @param IndexManagementInterface $indexManagement
     * @param SerializerInterface $serializer
     * @param ObjectFactory $objectFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        IndexManagementInterface $indexManagement,
        SerializerInterface $serializer,
        ObjectFactory $objectFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->indexManagement = $indexManagement;
        $this->serializer = $serializer;
        $this->objectFactory = $objectFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Process
     *
     * @param OperationInterface $operation
     *
     * @return void
     */
    public function process(OperationInterface $operation)
    {
        try {
            $serializedData = $operation->getSerializedData();
            $data = $this->serializer->unserialize($serializedData);
            /** @var DataObject $dataObject */
            $dataObject = $this->objectFactory->create(DataObject::class, ['data' => $data]);

            $applicationHeaders = $dataObject->getData('application_headers') ?? [];
            if (isset($applicationHeaders['store_id'])) {
                $storeId = $applicationHeaders['store_id'];
                try {
                    $currentStoreId = $this->storeManager->getStore()->getId();
                } catch (NoSuchEntityException $e) {
                    $errorMessage = sprintf(
                        "Can't set currentStoreId during processing queue message. Message rejected. Error %s.",
                        $e->getMessage()
                    );
                    $this->logger->error($errorMessage);
                    throw new \LogicException($errorMessage);
                }

                if (isset($storeId) && $storeId !== $currentStoreId) {
                    $this->storeManager->setCurrentStore($storeId);
                }
            }

            $this->execute($dataObject);

            if (isset($storeId, $currentStoreId) && $storeId !== $currentStoreId) {
                //restore original store value
                $this->storeManager->setCurrentStore($currentStoreId);
            }
        } catch (\Exception $e) {
            //@TODO reject message
        }
    }

    /**
     * Execute
     *
     * @param DataObject $data
     *
     * @return void
     */
    private function execute($data): void
    {
        $class = $data->getClass();
        $method = $data->getMethod();
        $arguments = $data->getData('method_arguments') ?? [];

        if (!$class) {
            return;
        }

        $object = $this->objectFactory->create($class, []);

        if (is_callable([$object, $method])) {
            call_user_func_array([$object, $method], $arguments);
        }
    }
}
