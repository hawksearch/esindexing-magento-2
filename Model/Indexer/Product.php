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

namespace HawkSearch\EsIndexing\Model\Indexer;

use HawkSearch\EsIndexing\Model\Config\Indexing;
use HawkSearch\EsIndexing\Model\Indexing\Entity\EntityTypePoolInterface;
use HawkSearch\EsIndexing\Model\Indexing\Entity\Type\ProductEntityType;
use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractItemsIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * Product constructor.
     * @param BulkPublisherInterface $publisher
     * @param StoreManagerInterface $storeManager
     * @param Indexing $indexingConfig
     * @param EntityTypePoolInterface $entityTypePool
     * @param ManagerInterface $eventManager
     * @param MessageTopicResolverInterface $messageTopicResolver
     * @param IndexManagementInterface $indexManagement
     * @param ProductDataProvider $productDataProvider
     */
    public function __construct(
        BulkPublisherInterface $publisher,
        StoreManagerInterface $storeManager,
        Indexing $indexingConfig,
        EntityTypePoolInterface $entityTypePool,
        ManagerInterface $eventManager,
        MessageTopicResolverInterface $messageTopicResolver,
        IndexManagementInterface $indexManagement,
        ProductDataProvider $productDataProvider
    ) {
        parent::__construct(
            $publisher,
            $storeManager,
            $indexingConfig,
            $entityTypePool,
            $eventManager,
            $messageTopicResolver,
            $indexManagement
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
     * @throws InputException
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
     * @inheritDoc
     */
    protected function getEntityTypeName()
    {
        return ProductEntityType::ENTITY_TYPE_NAME;
    }
}
