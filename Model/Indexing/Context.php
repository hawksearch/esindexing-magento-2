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

class Context implements ContextInterface
{
    /**
     * @var array
     */
    private $indexNameCache = [];

    /**
     * @var bool
     */
    private $isFullReindex;

    /**
     * @inheritDoc
     */
    public function setIndexName(int $storeId, string $indexName)
    {
        $this->indexNameCache[$storeId] = $indexName;
    }

    /**
     * @inheritDoc
     */
    public function getIndexName(int $storeId)
    {
        return $this->indexNameCache[$storeId] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function setIsFullReindex(bool $isFull)
    {
        $this->isFullReindex = $isFull;
    }

    /**
     * @inheritDoc
     */
    public function isFullReindex()
    {
        return $this->isFullReindex;
    }
}
