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

namespace HawkSearch\EsIndexing\Plugin\Page;

use HawkSearch\EsIndexing\Model\Indexer\ContentPage as ContentPageIndexer;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;

class PagePlugin
{
    private IndexerInterface $pageIndexer;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->pageIndexer = $indexerRegistry->get(ContentPageIndexer::INDEXER_ID);
    }

    /**
     * Reindex on page save.
     *
     * @throws \Exception
     */
    public function aroundSave(PageResource $pageResource, \Closure $proceed, AbstractModel $object): PageResource
    {
        return $this->addCommitCallback($pageResource, $proceed, $object);
    }

    /**
     * Reindex on product delete
     *
     * @throws \Exception
     */
    public function aroundDelete(PageResource $pageResource, \Closure $proceed, AbstractModel $object): PageResource
    {
        return $this->addCommitCallback($pageResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @throws \Exception
     */
    private function addCommitCallback(
        PageResource $pageResource,
        \Closure $proceed,
        AbstractModel $object
    ): PageResource {
        try {
            $pageResource->beginTransaction();
            $result = $proceed($object);
            $pageResource->addCommitCallback(function () use ($object) {
                $this->reindexRow((int)$object->getId());
            });
            $pageResource->commit();
        } catch (\Exception $e) {
            $pageResource->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Reindex page if indexer is not scheduled
     */
    private function reindexRow(int $pageId): void
    {
        if (!$this->pageIndexer->isScheduled()) {
            $this->pageIndexer->reindexRow($pageId);
        }
    }
}
