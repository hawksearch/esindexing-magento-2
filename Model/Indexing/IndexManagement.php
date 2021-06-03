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

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\EsIndexing\Api\Data\EsIndexInterface;
use HawkSearch\EsIndexing\Api\Data\IndexListInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;

class IndexManagement implements IndexManagementInterface
{
    /**
     * @var string
     */
    private $indicesListCacheId = 'hawksearch_indices_list_cache_tag';

    /**
     * @var array
     */
    private $indicesListCache;

    /**
     * @var string
     */
    private $currentIndexCache;

    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * @var ConfigCache
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * IndexManagement constructor.
     * @param InstructionManagerPool $instructionManagerPool
     * @param ConfigCache $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        InstructionManagerPool $instructionManagerPool,
        ConfigCache $cache,
        SerializerInterface $serializer
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function initializeFullReindex()
    {
        $indexName = $this->getIndexName();

        // remove non current index
        if ($indexName) {
            $this->removeIndex($indexName);
        }

        $this->createIndex();
    }

    /**
     * @inheritDoc
     */
    public function getIndexName($useCurrent = false) : ?string
    {
        $indices = $this->getIndices();
        $currentIndex = $this->getCurrentIndex();

        if ($useCurrent) {
            return $currentIndex;
        }

        $selectedIndex = null;
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function switchIndices()
    {
        $indexName = $this->getIndexName();
        if ($indexName) {
            $this->setCurrentIndex($indexName);
            $this->resetIndexCache();
        }
    }

    /**
     * Get current index used for searching
     * @return string|null
     * @throws InstructionException
     * @throws NotFoundException
     */
    private function getCurrentIndex() : ?string
    {
        if ($this->currentIndexCache === null) {
            /** @var EsIndexInterface $result */
            $result = $this->instructionManagerPool->get('hawksearch-esindexing')
                ->executeByCode('getCurrentIndex')->get();

            $this->addIndexToCache($result->getIndexName(), true);
        }

        return $this->currentIndexCache;
    }

    /**
     * @return array
     * @throws InstructionException
     * @throws NotFoundException
     */
    private function getIndices()
    {
        if ($this->indicesListCache === null) {
            /** @var IndexListInterface $indexList */
            $indexList = $this->instructionManagerPool->get('hawksearch-esindexing')
                ->executeByCode('getIndexList')->get();

            foreach ($indexList->getIndexNames() as $indexName) {
                $this->addIndexToCache($indexName);
            }
        }

        return (array)$this->indicesListCache;
    }

    /**
     * @param string $indexName
     * @throws InstructionException
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
     */
    private function resetIndexCache()
    {
        $this->indicesListCache = null;
        $this->currentIndexCache = null;
    }

    /**
     * @param string $index
     * @param bool $isCurrent
     */
    private function addIndexToCache(string $index, bool $isCurrent = false)
    {
        $this->indicesListCache[$index] = $index;
        if ($isCurrent) {
            $this->currentIndexCache = $index;
        }
    }

    /**
     * @param string $index
     */
    private function removeIndexFromCache(string $index)
    {
        unset($this->indicesListCache[$index]);
        if ($this->currentIndexCache === $index) {
            $this->currentIndexCache = null;
        }
    }

    /**
     * @inheritdoc
     */
    public function indexItems(array $items, string $indexName)
    {
        $data = [
            'IndexName' => $indexName,
            'Items' => array_values($items)
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('indexItems', $data)->get();
    }

    /**
     * @inheritdoc
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

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteItems', $data)->get();
    }

    /**
     * @inheritdoc
     */
    public function upsertHierarchy(array $items, string $indexName)
    {
        $data = [
            'IndexName' => $indexName,
            'Hierarchies' => array_values($items)
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('upsertHierarchy', $data)->get();
    }

    /**
     * @inheritdoc
     */
    public function rebuildHierarchy(string $indexName = null)
    {
        $data = [
            'IndexName' => $indexName
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('rebuildHierarchy', $data)->get();
    }

    /**
     * @inheritdoc
     */
    public function deleteHierarchyItems(array $ids, string $indexName)
    {
        if (!$ids) {
            return;
        }

        $data = [
            'IndexName' => $indexName,
            'Ids' => array_values($ids)
        ];

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteHierarchyItems', $data)->get();
    }
}
