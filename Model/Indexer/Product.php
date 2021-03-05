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

namespace HawkSearch\EsIndexing\Model\Indexer;

use HawkSearch\EsIndexing\Model\Config\General;
use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\EntityIndexerPoolInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsProviderPoolInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\PublisherInterface;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractItemsIndexer implements IndexerActionInterface, MviewActionInterface
{
    public const ENTITY_INDEXER_CODE = 'product';

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * Product constructor.
     * @param PublisherInterface $publisher
     * @param StoreManagerInterface $storeManager
     * @param Indexing $indexingConfig
     * @param General $generalConfig
     * @param EntityIndexerPoolInterface $entityIndexerPool
     * @param ProductDataProvider $productDataProvider
     * @param ItemsProviderPoolInterface $itemsProviderPool
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        PublisherInterface $publisher,
        StoreManagerInterface $storeManager,
        Indexing $indexingConfig,
        General $generalConfig,
        EntityIndexerPoolInterface $entityIndexerPool,
        ProductDataProvider $productDataProvider,
        ItemsProviderPoolInterface $itemsProviderPool,
        ManagerInterface $eventManager
    ) {
        parent::__construct(
            $publisher,
            $storeManager,
            $indexingConfig,
            $generalConfig,
            $entityIndexerPool,
            $itemsProviderPool,
            $eventManager
        );
        $this->productDataProvider = $productDataProvider;
    }

    /**
     * @inheritDoc
     */
    public function executeFull()
    {
        $this->execute(null);
    }

    /**
     * @inheritDoc
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @inheritDoc
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function execute($ids)
    {
        if ($ids) {
            $ids = array_merge($ids, $this->productDataProvider->getParentProductIds($ids));
        }

        if (is_array($ids) && count($ids) > 0) {
            $this->rebuildDelta($ids);
        } else {
            $this->rebuildFull();
        }
    }

    /**
     * @return string
     */
    public function getEntityIndexerCode()
    {
        return self::ENTITY_INDEXER_CODE;
    }
}
