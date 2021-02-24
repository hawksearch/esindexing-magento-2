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

namespace HawkSearch\EsIndexing\Model\Indexer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class ContentPage extends AbstractItemsIndexer implements IndexerActionInterface, MviewActionInterface
{
    public const ENTITY_INDEXER_CODE = 'content_page';

    /**
     * @inheritdoc
     */
    public function getEntityIndexerCode()
    {
        return self::ENTITY_INDEXER_CODE;
    }

    /**
     * @inheritdoc
     */
    public function executeFull()
    {
        $this->execute(null);
    }

    /**
     * @inheritdoc
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @inheritdoc
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @inheritdoc
     * @param $ids
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function execute($ids)
    {
        if (is_array($ids) && count($ids) > 0) {
            $this->rebuildDelta($ids);
        } else {
            $this->rebuildFull();
        }
    }
}
