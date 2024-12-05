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

namespace HawkSearch\EsIndexing\Plugin\ImportExport;

use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\ImportExport\Model\Import;

class ImportPlugin
{
    /**
     * @var IndexerInterface
     */
    private $productIndexer;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
    }

    /**
     * Invalidate index after import
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(Import $subject, mixed $import)
    {
        if (!$this->productIndexer->isScheduled()) {
            $this->productIndexer->invalidate();
        }

        return $import;
    }
}
