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

        $this->indicesListCache = null;
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
            ->executeByCode('deleteIndex', $data)->get();
    }

    /**
     * @inheritDoc
     */
    public function createIndex()
    {
        /** @var EsIndexInterface $result */
        $result = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('createIndex')->get();

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
            $this->currentIndexCache = null;
            $this->indicesListCache = null;
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

            $this->currentIndexCache = $result->getIndexName();
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

            $this->indicesListCache = $indexList->getIndexNames();
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
    }
}
