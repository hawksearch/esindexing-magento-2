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

namespace HawkSearch\EsIndexing\Model\Indexing\ItemsIndexer;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPoolInterface;
use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * The items indexer used for updating item changes in Elastic based index
 */
class SearchedItemsIndexer implements ItemsIndexerInterface
{
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private $instructionManagerPool;

    /**
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function add(array $items, string $indexName)
    {
        $this->update($items, $indexName);
    }

    /**
     * Uses api/v2/indexing/index-ixtems API call for items indexing
     *
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function update(array $items, string $indexName)
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
     * Uses api/v2/indexing/delete-items API call for removing items from the index
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function delete(array $items, string $indexName)
    {
        if (!$items) {
            return;
        }

        $data = [
            'IndexName' => $indexName,
            'Ids' => array_values($items)
        ];

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteItems', $data)->get();
    }
}
