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

use HawkSearch\EsIndexing\Api\HierarchyManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;

/**
 * The items indexer used for updating hierarchy items
 */
class HierarchyItemsIndexer implements ItemsIndexerInterface
{
    /**
     * @var HierarchyManagementInterface
     */
    private $hierarchyManagement;

    public function __construct(
        HierarchyManagementInterface $hierarchyManagement
    ) {
        $this->hierarchyManagement = $hierarchyManagement;
    }

    /**
     * @inheritDoc
     */
    public function add(array $items, string $indexName)
    {
        $this->update($items, $indexName);
    }

    /**
     * Uses hierarchy API to upsert hierarchy items
     */
    public function update(array $items, string $indexName)
    {
        $this->hierarchyManagement->upsertHierarchy(array_values($items), $indexName);
    }

    /**
     * Uses hierarchy API to remove hierarchy items
     */
    public function delete(array $items, string $indexName)
    {
        $this->hierarchyManagement->deleteHierarchyItems($items, $indexName);
    }
}
