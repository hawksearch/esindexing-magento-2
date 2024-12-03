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
use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * @api
 * @since 0.8.0
 */
class HierarchyManagement implements HierarchyManagementInterface
{
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;

    /**
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool
    )
    {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     * @throws InstructionException
     */
    public function upsertHierarchy(array $items, string $indexName)
    {
        if (!$items) {
            return;
        }

        $data = [
            'IndexName' => $indexName,
            'Hierarchies' => $items
        ];

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('upsertHierarchy', $data)->get();
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     * @throws InstructionException
     */
    public function rebuildHierarchy(string $indexName)
    {
        $data = [
            'IndexName' => $indexName
        ];

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('rebuildHierarchy', $data)->get();
    }

    /**
     * @inheritDoc
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

        $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteHierarchyItems', $data)->get();
    }
}
