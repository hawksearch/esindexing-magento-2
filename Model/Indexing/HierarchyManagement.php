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

class HierarchyManagement implements HierarchyManagementInterface
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * HierarchyManagement constructor.
     * @param IndexManagementInterface $indexManagement
     */
    public function __construct(
        IndexManagementInterface $indexManagement
    ){
        $this->indexManagement = $indexManagement;
    }



    /**
     * @inheritDoc
     */
    public function upsertHierarchy(array $items, string $indexName)
    {
        // TODO: Implement upsertHierarchy() method.
    }

    /**
     * @inheritDoc
     */
    public function rebuildHierarchy(string $indexName = null)
    {
        // TODO: Implement rebuildHierarchy() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteHierarchyItems(array $ids, string $indexName)
    {
        // TODO: Implement deleteHierarchyItems() method.
    }

    /**
     * @inheritDoc
     */
    public function initializeHierarchyIndexing(bool $useCurrentIndex)
    {
        // TODO: Implement initializeHierarchyIndexing() method.
    }
}
