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

namespace HawkSearch\EsIndexing\Api;

/**
 * Interface for managing Hierarchy in HawkSearch
 *
 * @api
 * @since 0.8.0
 */
interface HierarchyManagementInterface
{
    /**
     * Upsert Hierarchy
     * @param list<array<string, mixed>> $items
     * @param string $indexName
     * @return void
     * @todo use \HawkSearch\EsIndexing\Api\Data\HierarchyInterface[] as input array
     */
    public function upsertHierarchy(array $items, string $indexName);

    /**
     * Rebuild index hierarchy
     * @param string $indexName
     * @return void
     */
    public function rebuildHierarchy(string $indexName);

    /**
     * Removes multiple documents from a specific hierarchy based on ids
     * @param string[] $ids
     * @param string $indexName
     * @return void
     */
    public function deleteHierarchyItems(array $ids, string $indexName);
}
