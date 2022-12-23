<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\EsIndexing\Api\Data\EsIndexInterface;
use Magento\Framework\Exception\NotFoundException;

interface IndexManagementInterface
{
    /**
     * Start process of full reindexing
     * We consider that number of indices per engine is equal to 2
     * Non current index is the only index which can be processed during full reindexing
     * @return void
     */
    public function initializeFullReindex();

    /**
     * Get active index for indexing
     * Use current index for partial delta index updates
     * Current index is used for searching
     * @param bool $useCurrent
     * @return string
     */
    public function getIndexName($useCurrent = false) : ?string;

    /**
     * Remove index
     * @param string $indexName
     * @return void
     */
    public function removeIndex(string $indexName);

    /**
     * @return EsIndexInterface
     */
    public function createIndex();

    /**
     * @return void
     */
    public function switchIndices();

    /**
     * @param array $items
     * @param string $indexName
     * @return void
     */
    public function indexItems(array $items, string $indexName);

    /**
     * @param array $ids
     * @param string $indexName
     * @return void
     */
    public function deleteItems(array $ids, string $indexName);
}
