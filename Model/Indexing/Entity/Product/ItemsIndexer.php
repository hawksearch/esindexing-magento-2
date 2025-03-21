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

use Exception;
use HawkSearch\Connector\Gateway\Http\Converter\ArrayToJson as ArrayToJsonConverter;
use HawkSearch\EsIndexing\Model\DataIndex;
use HawkSearch\EsIndexing\Model\DataPreloadItems as DataPreloadItemsModel;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface as IndexingContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex as DataIndexResource;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\Collection as DataIndexCollection;
use HawkSearch\EsIndexing\Model\ResourceModel\DataIndex\CollectionFactory as DataIndexCollectionFactory;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems as DataPreloadItemsResource;
use Magento\Store\Model\StoreManagerInterface;


/**
 * @experimental
 * @internal experimental
 */
class ItemsIndexer implements ItemsIndexerInterface
{
    private $loadedIndexCache = [];
    private string $indexName;
    private ArrayToJsonConverter $converter;
    private DataPreloadItemsResource $dataPreloadItemsResource;
    private DataIndexResource $dataIndexResource;
    private MessageManagerInterface $messageManager;
    private MessageTopicResolverInterface $messageTopicResolver;
    private DataIndexCollectionFactory $dataIndexCollectionFactory;
    private StoreManagerInterface $storeManager;
    private IndexingContextInterface $indexingContext;

    public function __construct(
        ArrayToJsonConverter $converter,
        DataPreloadItemsResource $dataPreloadItemsResource,
        DataIndexResource $dataIndexResource,
        MessageManagerInterface $messageManager,
        MessageTopicResolverInterface $messageTopicResolver,
        DataIndexCollectionFactory $dataIndexCollectionFactory,
        StoreManagerInterface $storeManager,
        IndexingContextInterface $indexingContext
    ) {
        $this->converter = $converter;
        $this->dataPreloadItemsResource = $dataPreloadItemsResource;
        $this->dataIndexResource = $dataIndexResource;
        $this->messageManager = $messageManager;
        $this->messageTopicResolver = $messageTopicResolver;
        $this->dataIndexCollectionFactory = $dataIndexCollectionFactory;
        $this->storeManager = $storeManager;
        $this->indexingContext = $indexingContext;
    }

    public function add(array $items, string $indexName): void
    {
        $this->update($items, $indexName);
    }

    public function update(array $items, string $indexName): void
    {
        $this->indexName = $indexName;
        $this->processItems($items, 'index');
    }

    public function delete(array $items, string $indexName): void
    {
        $this->indexName = $indexName;
        $this->processItems($items, 'delete');
    }

    /**
     * @param array<mixed> $items
     * @param string $method
     */
    private function processItems(array $items, string $method): void
    {
        $items = array_values($items);

        if (!$items) {
            return;
        }

        if (!$indexId = $this->loadDataIndexByName($this->indexName)->getId()) {
            //@todo throw an exception here just not to make it silent
            return;
        }

        if ($this->indexName != $this->indexingContext->getIndexName($this->getStoreId())) {
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

        $insertData = [];
        for ($page = 1; $page <= $itemsBatches; $page++) {
            $insertData[] = [
                'index_id' => $indexId,
                'method' => $method,
                'status' => DataPreloadItemsModel::STATUS_TYPE_OPEN,
                'request' => $this->converter->convert([
                    'IndexName' => $this->indexName,
                    $dataFieldsMap[$method] => $itemsChunks[$page - 1]
                ])
            ];
        }

        $this->saveMultipleRows($insertData);
    }

    /**
     * @param list<array<string, mixed>> $data
     * @throws Exception
     */
    private function saveMultipleRows(array $data): void
    {
        $dataIds = [];
        try {
            $this->dataPreloadItemsResource->getConnection()->beginTransaction();

            foreach ($data as $row) {
                $this->dataPreloadItemsResource->addCommitCallback(function () use ($row, &$dataIds) {
                    $this->dataPreloadItemsResource->getConnection()->insert(
                        $this->dataPreloadItemsResource->getMainTable(),
                        $row
                    );
                    $dataIds[] = (int)$this->dataPreloadItemsResource->getConnection()->lastInsertId(
                        $this->dataPreloadItemsResource->getMainTable()
                    );
                });
            }

            $this->dataPreloadItemsResource->getConnection()->commit();
        } catch (\Exception $e) {
            $this->dataPreloadItemsResource->getConnection()->rollBack();
            throw $e;
        }

        if (count($dataIds)) {
            $dataIndex = $this->loadDataIndexByName($this->indexName);
            $dataIndex->setStage2Scheduled($dataIndex->getStage2Scheduled() + count($dataIds));
            $this->dataIndexResource->save($dataIndex);
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
                    'index' => $this->indexName
                ]
            );
        }

    }

    private function loadDataIndexByName(string $indexName): DataIndex
    {
        if (!isset($this->loadedIndexCache[$indexName][$this->getStoreId()])) {
            /** @var DataIndexCollection $collection */
            $collection = $this->dataIndexCollectionFactory->create()
                ->addFieldToFilter('engine_index_name', $indexName)
                ->addFieldToFilter('store_id', $this->getStoreId());

            $this->loadedIndexCache[$indexName][$this->getStoreId()] = $collection->getFirstItem();
        }

        /** @var DataIndex */
        return $this->loadedIndexCache[$indexName][$this->getStoreId()];
    }

    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
