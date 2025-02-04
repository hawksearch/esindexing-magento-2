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

namespace HawkSearch\EsIndexing\Plugin;

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\Consumer;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ConsumerProcessorPlugin
{
    private SerializerInterface $serializer;
    private StoreManagerInterface $storeManager;
    private ContextInterface $indexingContext;
    private Emulation $emulation;

    public function __construct(
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        Emulation $emulation
    ) {
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
        $this->emulation = $emulation;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeProcess(Consumer $subject, QueueOperationDataInterface $operation): void
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

        $isFullReindex = $applicationHeaders['full_reindex'] ?? false;
        $this->indexingContext->setIsFullReindex($isFullReindex);
    }
}
