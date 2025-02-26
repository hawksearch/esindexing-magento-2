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
    /**
     * @var array<int, ?array<string, string>>
     */
    private array $indicesListCache = [];
    /**
     * @var array<int, ?string>
     */
    private array $currentIndexCache = [];

    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;
    private LoggerInterface $hawkLogger;
    private StoreManagerInterface $storeManager;

    /**
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     * @param LoggerFactoryInterface $loggerFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
        $this->hawkLogger = $loggerFactory->create();
        $this->storeManager = $storeManager;
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

    /**
     * @return string|null
     */
    public function getIndexName(bool $useCurrent = false): ?string
    {
        $indices = $this->getIndices();
        $currentIndex = $indices ? $this->getCurrentIndex() : '';

        if ($useCurrent) {
            return $currentIndex;
        }

        $selectedIndex = '';
        foreach ($indices as $indexName) {
            if ($indexName === $currentIndex) {
                continue;
            }
            $selectedIndex = $indexName;
            break;
        }

        return $selectedIndex;
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

        $this->removeIndexFromCache($indexName);
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

        $this->addIndexToCache($result->getIndexName());

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
            $this->hawkLogger->info(sprintf("Non current index selected: %s", $indexName));
            $this->setCurrentIndex($indexName);
            $this->resetIndexCache();
        } else {
            $this->hawkLogger->info(sprintf("There is no temporary index in HawkSearch engine yet."));
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
     * Get current index used for searching
     *
     * @return string|null
     * @throws InstructionException
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    private function getCurrentIndex(): ?string
    {
        $indexFromCache = current($this->getIndicesFromCache(true));
        if ($indexFromCache === false || $indexFromCache === null) {
            /** @var EsIndexInterface $result */
            $result = $this->instructionManagerPool->get('hawksearch-esindexing')
                ->executeByCode('getCurrentIndex')->get();

            $indexFromCache = $result->getIndexName();
            $this->addIndexToCache($indexFromCache, true);
        }

        return $indexFromCache;
    }

    /**
     * @return array
     * @throws InstructionException
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    private function getIndices()
    {
        if (empty($this->getIndicesFromCache())) {
            /** @var IndexListInterface $indexList */
            $indexList = $this->instructionManagerPool->get('hawksearch-esindexing')
                ->executeByCode('getIndexList')->get();

            foreach ($indexList->getIndexNames() as $indexName) {
                $this->addIndexToCache($indexName);
            }
        }

        return $this->getIndicesFromCache();
    }

    /**
     * @throws InstructionException
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    private function setCurrentIndex(string $indexName)
    {
        $data = [
            EsIndexInterface::INDEX_NAME => $indexName
        ];
        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('setCurrentIndex', $data)->get();

        $this->addIndexToCache($indexName, true);
    }

    /**
     * Reset cached values
     *
     * @throws NoSuchEntityException
     * @TODO Replace with \Psr\Cache\CacheItemPoolInterface implementation
     */
    private function resetIndexCache()
    {
        $storeId = $this->getStoreId();
        $this->indicesListCache[$storeId] = null;
        $this->currentIndexCache[$storeId] = null;
    }

    /**
     * @throws NoSuchEntityException
     * @TODO Replace with {@see \Psr\Cache\CacheItemPoolInterface} implementation
     */
    private function addIndexToCache(string $index, bool $isCurrent = false)
    {
        $storeId = $this->getStoreId();

        $this->indicesListCache[$storeId] = $this->indicesListCache[$storeId] ?? [];
        $this->indicesListCache[$storeId][$index] = $index;

        if ($isCurrent) {
            $this->currentIndexCache[$storeId] = $index;
        }
    }

    /**
     * @throws NoSuchEntityException
     * @TODO Replace with \Psr\Cache\CacheItemPoolInterface implementation
     */
    private function removeIndexFromCache(string $index)
    {
        $storeId = $this->getStoreId();

        unset($this->indicesListCache[$storeId][$index]);
        if (isset($this->currentIndexCache[$storeId]) && $this->currentIndexCache[$storeId] === $index) {
            $this->currentIndexCache[$storeId] = null;
        }
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @TODO Replace with \Psr\Cache\CacheItemPoolInterface implementation
     */
    private function getIndicesFromCache(bool $isCurrent = false)
    {
        $storeId = $this->getStoreId();

        return $isCurrent
            ? (array)($this->currentIndexCache[$storeId] ?? null)
            : ($this->indicesListCache[$storeId] ?? []);
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
