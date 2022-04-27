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

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use HawkSearch\EsIndexing\Model\Indexing\Consumer;
use HawkSearch\EsIndexing\Model\Indexing\Context;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ConsumerProcessorPlugin
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Context
     */
    private $indexingContext;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * ConsumerProcessorPlugin constructor.
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     * @param Context $indexingContext
     * @param Emulation $emulation
     */
    public function __construct(
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        Context $indexingContext,
        Emulation $emulation
    ) {
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->emulation = $emulation;
    }

    /**
     * @param Consumer $subject
     * @param QueueOperationDataInterface $operation
     * @return null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeProcess(Consumer $subject, QueueOperationDataInterface $operation)
    {
        $data = $this->serializer->unserialize($operation->getData());

        $applicationHeaders = $data['application_headers'] ?? [];

        if (isset($applicationHeaders['store_id'])) {
            $this->emulation->startEnvironmentEmulation($applicationHeaders['store_id'], Area::AREA_FRONTEND, true);
        }

        if (!empty($applicationHeaders['index'])) {
            $this->indexingContext->setIndexName(
                (int)$this->storeManager->getStore()->getId(),
                $applicationHeaders['index']
            );
        }

        return null;
    }

    /**
     * Restore original store value
     * @param Consumer $subject
     * @param $result
     */
    public function afterProcess(Consumer $subject, $result)
    {
        $this->emulation->stopEnvironmentEmulation();
    }
}
