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
    /**
     * @var IndexerInterface
     */
    private $pageIndexer;

    /**
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->pageIndexer = $indexerRegistry->get(ContentPageIndexer::INDEXER_ID);
    }

    /**
     * Reindex on page save.
     *
     * @param PageResource $pageResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return PageResource
     * @throws \Exception
     */
    public function aroundSave(PageResource $pageResource, \Closure $proceed, AbstractModel $object)
    {
        return $this->addCommitCallback($pageResource, $proceed, $object);
    }

    /**
     * Reindex on product delete
     *
     * @param PageResource $pageResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return PageResource
     * @throws \Exception
     */
    public function aroundDelete(PageResource $pageResource, \Closure $proceed, AbstractModel $object)
    {
        return $this->addCommitCallback($pageResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @param PageResource $pageResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return PageResource
     * @throws \Exception
     */
    private function addCommitCallback(PageResource $pageResource, \Closure $proceed, AbstractModel $object)
    {
        try {
            $pageResource->beginTransaction();
            $result = $proceed($object);
            $pageResource->addCommitCallback(function () use ($object) {
                $this->reindexRow($object->getId());
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
     *
     * @param int $pageId
     * @return void
     */
    protected function reindexRow($pageId)
    {
        if (!$this->pageIndexer->isScheduled()) {
            $this->pageIndexer->reindexRow($pageId);
        }
    }
}
