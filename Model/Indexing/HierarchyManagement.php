<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

class HierarchyManagement implements HierarchyManagementInterface
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * HierarchyManagement constructor.
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        IndexManagementInterface $indexManagement,
        InstructionManagerPool $instructionManagerPool
    ){
        $this->indexManagement = $indexManagement;
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @inheritDoc
     */
    public function upsertHierarchy(array $items, string $indexName)
    {
        $hierarchies = array_values($items);

        if ($hierarchies) {
            $data = [
                'IndexName' => $indexName,
                'Hierarchies' => $hierarchies
            ];

            $response = $this->instructionManagerPool
                ->get('hawksearch-esindexing')->executeByCode('upsertHierarchy', $data)->get();
        }
    }

    /**
     * @inheritDoc
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

        $response = $this->instructionManagerPool
            ->get('hawksearch-esindexing')->executeByCode('deleteHierarchyItems', $data)->get();
    }

    /**
     * @inheritDoc
     */
    public function initializeHierarchyIndexing(bool $useCurrentIndex)
    {
        // TODO: Implement initializeHierarchyIndexing() method.
    }
}
