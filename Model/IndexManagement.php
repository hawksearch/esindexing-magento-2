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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPoolInterface;
use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\EsIndexing\Api\Data\EsIndexInterface;
use HawkSearch\EsIndexing\Api\Data\IndexListInterface;
use HawkSearch\EsIndexing\Api\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\Collection as DataIndexCollection;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\CollectionFactory as DataIndexCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @api
 * @since 0.8.0
 */
class IndexManagement implements IndexManagementInterface
{
    private const MAX_VALID_COUNT = 2;
    private const MAX_CURRENT_COUNT = 1;
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;
    private LoggerInterface $hawkLogger;
    private StoreManagerInterface $storeManager;
    private DataIndexCollectionFactory $dataIndexCollectionFactory;
    private DataIndexCollection $storeDataIndexCollection;

    /**
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     * @param DataIndexCollectionFactory|null $dataIndexCollectionFactory
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        DataIndexCollectionFactory $dataIndexCollectionFactory = null
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
        $this->hawkLogger = $loggerFactory->create();
        $this->storeManager = $storeManager;
        $this->dataIndexCollectionFactory = $dataIndexCollectionFactory ?: ObjectManager::getInstance()->get(DataIndexCollectionFactory::class);
    }

    /**
     * @return void
     */
    public function initializeFullReindex()
    {
        $this->hawkLogger->info("--- initializeFullReindex STARTED ---");

        $indexName = $this->getIndexName();
        $this->hawkLogger->info(sprintf("Non current index selected: %s", $indexName));

        // remove non current index
        if ($indexName) {
            $this->removeIndex($indexName);
        }

        $newIndex = $this->createIndex();
        $this->hawkLogger->info(sprintf("New index created: %s", $newIndex->getIndexName()));

        $this->hawkLogger->info("--- initializeFullReindex FINISHED ---");
    }

    public function getIndexName(bool $useCurrent = false): ?string
    {
        if (!$this->getStoreDataIndexCollection()->isLoaded()) {
            $this->synchronizeIndices();
        }

        $currentIndex = null;
        $nonCurrentIndex = null;
        foreach ($this->getStoreDataIndices() as $index) {
            if ($index->getIsCurrent() && !$currentIndex) {
                $currentIndex = $index->getEngineIndexName();
            } elseif (!$index->getIsCurrent() && !$nonCurrentIndex) {
                $nonCurrentIndex = $index->getEngineIndexName();
            }
        }

        if ($useCurrent) {
            return $currentIndex;
        } else {
            return $nonCurrentIndex;
        }
    }

    /**
     * @return void
     * @throws InstructionException
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function removeIndex(string $indexName)
    {
        $data = [
            EsIndexInterface::INDEX_NAME => $indexName
        ];
        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('deleteIndex', $data);
        $this->synchronizeIndices();
    }

    /**
     * @return EsIndexInterface
     * @throws InstructionException
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function createIndex()
    {
        /** @var EsIndexInterface $result */
        $result = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('createIndex')->get();
        $this->synchronizeIndices();
        return $result;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws NotFoundException
     * @throws InstructionException
     */
    public function switchIndices()
    {
        $this->hawkLogger->info("--- switchIndices STARTED ---");

        $indexName = $this->getIndexName();
        if ($indexName) {
            $this->hawkLogger->info(sprintf("Non-current index is selected: %s", $indexName));
            $this->setCurrentIndex($indexName);
            $this->synchronizeIndices();
        } else {
            $this->hawkLogger->info("There is no temporary index in HawkSearch engine yet.");
        }

        $this->hawkLogger->info("--- switchIndices FINISHED ---");
    }

    /**
     * @return void
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function indexItems(array $items, string $indexName)
    {
        $items = array_values($items);

        if (!$items) {
            return;
        }

        $data = [
            'IndexName' => $indexName,
            'Items' => $items
        ];

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('indexItems', $data)->get();
    }

    /**
     * @return void
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function deleteItems(array $ids, string $indexName)
    {
        if (!$ids) {
            return;
        }

        $data = [
            'IndexName' => $indexName,
            'Ids' => array_values($ids)
        ];

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteItems', $data)->get();
    }

    /**
     * Get current index used for searching from external API
     *
     * @throws InstructionException
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    private function getCurrentIndex(): ?string
    {
        /** @var EsIndexInterface $result */
        $result = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getCurrentIndex')->get();
        return $result->getIndexName();
    }

    private function getAvailableIndices(): IndexListInterface
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getIndexList')->get();
    }

    /**
     * Synchronize table cache with external service and return valid index names
     *
     * @throws InstructionException
     * @throws NoSuchEntityException
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function synchronizeIndices(): void
    {
        $storeId = $this->getStoreId();

        // 1. Fetch from external service
        $externalIndices = $this->getAvailableIndices()->getIndexNames();
        $externalCurrent = $this->getCurrentIndex();

        // 2. Load all DB rows for this store
        $storeDataIndexCollection = $this->getStoreDataIndexCollection(true);
        $storeDataIndexNames = [];

        $validCount = 0;
        $currentCount = 0;

        $connection = $storeDataIndexCollection->getConnection();
        try {
            $connection->beginTransaction();

            // 3. Update existing collection rows to match external state
            /** @var DataIndex $dataIndex */
            foreach ($storeDataIndexCollection as $dataIndex) {
                $dataIndex->afterLoad();
                $name = $dataIndex->getEngineIndexName();
                $wasValid = (bool)$dataIndex->getIsValid();
                $wasCurrent = (bool)$dataIndex->getIsCurrent();
                $isValid = in_array($name, $externalIndices, true);
                $isCurrent = ($name === $externalCurrent && $isValid);

                $logChanges = false;
                if ($wasValid !== $isValid) {
                    $dataIndex->setIsValid($isValid);
                    $logChanges = true;
                }
                if ($wasCurrent !== $isCurrent) {
                    $dataIndex->setIsCurrent($isCurrent);
                    $logChanges = true;
                }
                if ($logChanges) {
                    $this->hawkLogger->info(sprintf(
                        "Sync indices: Updating DB index '%s' for store %d: is_valid %d→%d, is_current %d→%d",
                        $name, $storeId, (int)$wasValid, (int)$isValid, (int)$wasCurrent, (int)$isCurrent
                    ));
                }

                if ($isValid) {
                    $validCount++;
                }
                if ($isCurrent) {
                    $currentCount++;
                }
                $storeDataIndexNames[$name] = $dataIndex;
            }

            // 4. Insert missing external indices into collection
            foreach ($externalIndices as $name) {
                if (isset($storeDataIndexNames[$name])) {
                    continue;
                }

                $this->hawkLogger->info(sprintf(
                    "Sync indices: Inserting missing external index '%s' for store %d (is_current=%d)",
                    $name, $storeId, ($name === $externalCurrent ? 1 : 0)
                ));

                $dataIndex = $storeDataIndexCollection->getNewEmptyItem();
                $dataIndex->setEngineIndexName($name)
                    ->setStoreId($storeId)
                    ->setIsValid(true)
                    ->setIsCurrent($name === $externalCurrent);
                $storeDataIndexCollection->addItem($dataIndex);
                if ($name === $externalCurrent) {
                    $currentCount++;
                }
                $validCount++;
            }

            // 5. Save collection state to the DB and reset collection
            $storeDataIndexCollection->save();
            $this->getStoreDataIndexCollection(true);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }


        // 6. Enforce max valid and current
        // (If more than allowed, log warning)
        if ($validCount > self::MAX_VALID_COUNT) {
            $this->hawkLogger->warning(sprintf(
                "More than %d valid indices detected for store %d", self::MAX_VALID_COUNT, $storeId
            ));
        }
        if ($currentCount > self::MAX_CURRENT_COUNT) {
            $this->hawkLogger->warning(sprintf(
                "More than %d current indices detected for store %d", self::MAX_CURRENT_COUNT, $storeId
            ));
        }
    }

    /**
     * @throws InstructionException
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    private function setCurrentIndex(string $indexName): void
    {
        $data = [
            EsIndexInterface::INDEX_NAME => $indexName
        ];
        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('setCurrentIndex', $data)->get();
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * @return DataIndex[]
     */
    private function getStoreDataIndices(): array
    {
        /** @var DataIndex[] */
        return $this->getStoreDataIndexCollection()
            ->addFieldToFilter('is_valid', ['eq' => 1])
            ->getItems();
    }

    private function getStoreDataIndexCollection(bool $reset = false): DataIndexCollection
    {
        if (!isset($this->storeDataIndexCollection) || $reset) {
            $this->storeDataIndexCollection = $this->dataIndexCollectionFactory->create();
            $this->storeDataIndexCollection->addFieldToFilter('store_id', ['eq' => $this->getStoreId()]);
        }

        return $this->storeDataIndexCollection;
    }
}
