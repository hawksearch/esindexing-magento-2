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

interface EntityIndexerInterface
{
    /**
     * @param int $storeId
     * @param array|null $entityIds
     * @return void
     */
    public function rebuildEntityIndex(int $storeId, $entityIds = null);

    /**
     * @param int $storeId
     * @param int $currentPage
     * @param int $pageSize
     * @param array|null $entityIds
     * @return void
     */
    public function rebuildEntityIndexBatch(int $storeId, int $currentPage, int $pageSize, ?array $entityIds = null);
}
