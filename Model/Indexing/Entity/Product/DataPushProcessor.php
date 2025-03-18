<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Product;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPoolInterface;
use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Model\DataPreloadItems;
use HawkSearch\EsIndexing\Model\DataPreloadItemsFactory;
use HawkSearch\EsIndexing\Model\Indexing\EntityType\ProductEntityType;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems as DataPreloadItemsResource;
use Psr\Log\LoggerInterface;

class DataPushProcessor
{
    private LoggerInterface $logger;
    private ProductEntityType $entityType;
    private DataPreloadItemsResource $dataPreloadItemsResource;
    private DataPreloadItemsFactory $dataPreloadItemsFactory;
    private InstructionManagerPoolInterface $instructionManagerPool;

    public function __construct(
        LoggerFactoryInterface $loggerFactory,
        ProductEntityType $entityType,
        DataPreloadItemsResource $dataPreloadItemsResource,
        DataPreloadItemsFactory $dataPreloadItemsFactory,
        InstructionManagerPoolInterface $instructionManagerPool
    ) {
        $this->logger = $loggerFactory->create();
        $this->entityType = $entityType;
        $this->dataPreloadItemsResource = $dataPreloadItemsResource;
        $this->dataPreloadItemsFactory = $dataPreloadItemsFactory;
        $this->instructionManagerPool = $instructionManagerPool;
    }

    public function execute(int $dataId): void
    {
        $this->logInfoStep(0, [$dataId]);

        $item = $this->loadData($dataId);
        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode(
                $this->getInstructionCodeByMethod($item->getMethod()),
                ['json' => $item->getRequest()]
            )->get();

        $this->logInfoStep(1, [$dataId]);
    }

    private function getInstructionCodeByMethod(string $method): string
    {
        $map = [
            'delete' => 'deleteItemsPush',
            'index' => 'indexItemsPush'
        ];

        return $map[$method] ?? '';
    }

    private function loadData(int $dataId): DataPreloadItems
    {
        $dataPreloadItem = $this->dataPreloadItemsFactory->create();
        $this->dataPreloadItemsResource->load($dataPreloadItem, $dataId);
        return $dataPreloadItem;
    }

    private function logInfoStep(int $step, array $data): void
    {
        $type = $this->entityType->getTypeName();
        $className = str_replace(__NAMESPACE__ . '\\', '', static::class);
        $steps = [
            // steps start from 0
            "$className - Start push - Entity: $type, dataId: %s",
            "$className - End push - Entity: $type, dataId: %s",
        ];

        if (!isset($steps[$step])) {
            return;
        }

        $this->logger->info(
            sprintf(
                $steps[$step],
                ...$data
            )
        );
    }
}
