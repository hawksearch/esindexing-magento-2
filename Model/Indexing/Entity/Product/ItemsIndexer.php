<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Product;

use HawkSearch\Connector\Gateway\Http\Converter\ArrayToJson as ArrayToJsonConverter;
use HawkSearch\EsIndexing\Model\DataIndex;
use HawkSearch\EsIndexing\Model\DataPreloadItems as DataPreloadItemsModel;
use HawkSearch\EsIndexing\Model\DataPreloadItemsFactory;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface as IndexingContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex as DataIndexResource;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\Collection as DataIndexCollection;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems as DataPreloadItemsResource;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems\Collection as DataPreloadItemsCollection;
use Magento\Framework\Exception\InputException;
use Magento\Store\Model\StoreManagerInterface;


/**
 * @experimental
 * @internal experimental
 */
class ItemsIndexer implements ItemsIndexerInterface
{
    private ArrayToJsonConverter $converter;
    private DataPreloadItemsResource $dataPreloadItemsResource;
    private DataPreloadItemsFactory $dataPreloadItemsFactory;
    private DataPreloadItemsCollection $dataPreloadItemsCollection;
    private DataIndexResource $dataIndexResource;
    private DataIndexCollection $dataIndexCollection;
    private MessageManagerInterface $messageManager;
    private MessageTopicResolverInterface $messageTopicResolver;
    private StoreManagerInterface $storeManager;
    private IndexingContextInterface $indexingContext;

    public function __construct(
        ArrayToJsonConverter $converter,
        DataPreloadItemsResource $dataPreloadItemsResource,
        DataPreloadItemsFactory $dataPreloadItemsFactory,
        DataPreloadItemsCollection $dataPreloadItemsCollection,
        DataIndexResource $dataIndexResource,
        DataIndexCollection $dataIndexCollection,
        MessageManagerInterface $messageManager,
        MessageTopicResolverInterface $messageTopicResolver,
        StoreManagerInterface $storeManager,
        IndexingContextInterface $indexingContext
    ) {
        $this->converter = $converter;
        $this->dataPreloadItemsResource = $dataPreloadItemsResource;
        $this->dataPreloadItemsFactory = $dataPreloadItemsFactory;
        $this->dataPreloadItemsCollection = $dataPreloadItemsCollection;
        $this->dataIndexResource = $dataIndexResource;
        $this->dataIndexCollection = $dataIndexCollection;
        $this->messageManager = $messageManager;
        $this->messageTopicResolver = $messageTopicResolver;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
    }

    public function add(array $items, string $indexName): void
    {
        $this->update($items, $indexName);
    }

    public function update(array $items, string $indexName): void
    {
        $this->processItems($items, 'index', $indexName);
    }

    public function delete(array $items, string $indexName): void
    {
        $this->processItems($items, 'delete', $indexName);
    }

    /**
     * @param array<mixed> $items
     * @param string $method
     */
    private function processItems(array $items, string $method, string $indexName): void
    {
        $items = array_values($items);

        if (!$items) {
            return;
        }

        if ($indexName != $this->indexingContext->getIndexName($this->getStoreId())) {
            //@todo throw an exception here just not to make it silent
            return;
        }

        $dataIndex = $this->loadDataIndexByName($indexName);
        if (!$dataIndex->getId()) {
            //@todo throw an exception here just not to make it silent
            return;
        }

        $dataFieldsMap = [
            'delete' => 'Ids',
            'index' => 'Items'
        ];

        $itemsBatchSize = 125;
        $itemsChunks = array_chunk($items, $itemsBatchSize);
        $itemsBatches = count($itemsChunks);

        for ($page = 1; $page <= $itemsBatches; $page++) {
            /** @var DataPreloadItemsModel $newItem */
            $newItem = $this->dataPreloadItemsFactory->create();
            $newItem->setIndexId($dataIndex->getId())
                ->setMethod($method)
                ->setStatus(DataPreloadItemsModel::STATUS_TYPE_OPEN)
                ->setRequest($this->converter->convert([
                    'IndexName' => $indexName,
                    $dataFieldsMap[$method] => $itemsChunks[$page - 1]
                ]));
            $this->dataPreloadItemsCollection->addItem($newItem);
        }

        $this->saveMultipleItems($dataIndex);
    }

    /**
     * @throws InputException
     */
    private function saveMultipleItems(DataIndex $dataIndex): void
    {
        $savedItems = $this->dataPreloadItemsCollection->saveAllNew();
        $dataIds = array_keys($savedItems);

        if (count($dataIds)) {
            $this->dataIndexResource->incrementStage2Scheduled($dataIndex, count($dataIds));
            $this->dataIndexResource->load($dataIndex, $dataIndex->getId());
        }

        foreach ($dataIds as $id) {
            $this->messageManager->addMessage(
                $this->messageTopicResolver->resolve($this),
                [
                    'class' => \HawkSearch\EsIndexing\Model\Indexing\Entity\Product\DataPushProcessor::class,
                    'method' => 'execute',
                    'method_arguments' => [
                        'dataId' => $id,
                    ],
                    'full_reindex' => $this->indexingContext->isFullReindex(),
                    'index' => $dataIndex->getEngineIndexName()
                ]
            );
        }

    }

    private function loadDataIndexByName(string $indexName): DataIndex
    {
        $this->dataIndexCollection->_resetState();

        /** @var DataIndex */
        return $this->dataIndexCollection->addFieldToFilter('engine_index_name', $indexName)
            ->addFieldToFilter('store_id', (string)$this->getStoreId())
            ->getFirstItem()->afterLoad();
    }

    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
