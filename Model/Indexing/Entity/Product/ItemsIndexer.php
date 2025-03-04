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
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageTopicResolverInterface;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadIndex;

/**
 * @experimental
 * @internal experimental
 */
class ItemsIndexer implements ItemsIndexerInterface
{
    private string $indexName;
    private ArrayToJsonConverter $converter;
    private DataPreloadIndex $dataPreloadIndexResource;
    private MessageManagerInterface $messageManager;
    private MessageTopicResolverInterface $messageTopicResolver;
    private BulkPublisherInterface $publisher;

    public function __construct(
        ArrayToJsonConverter $converter,
        DataPreloadIndex $dataPreloadIndexResource,
        MessageManagerInterface $messageManager,
        MessageTopicResolverInterface $messageTopicResolver,
        BulkPublisherInterface $publisher
    ) {
        $this->converter = $converter;
        $this->dataPreloadIndexResource = $dataPreloadIndexResource;
        $this->messageManager = $messageManager;
        $this->messageTopicResolver = $messageTopicResolver;
        $this->publisher = $publisher;
    }

    public function add(array $items, string $indexName): void
    {
        $this->update($items, $indexName);
    }

    public function update(array $items, string $indexName): void
    {
        $this->indexName = $indexName;
        $this->processItems($items, 'indexItems');
    }

    public function delete(array $items, string $indexName): void
    {
        $this->indexName = $indexName;
        $this->processItems($items, 'deleteItems');
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

        $dataFieldsMap = [
            'deleteItems' => 'Ids',
            'indexItems' => 'Items'
        ];

        $itemsBatchSize = 125;
        $itemsChunks = array_chunk($items, $itemsBatchSize);
        $itemsBatches = count($itemsChunks);

        $insertData = [];
        for ($page = 1; $page <= $itemsBatches; $page++) {
            $insertData[] = [
                'method' => $method,
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
            $this->dataPreloadIndexResource->getConnection()->beginTransaction();

            foreach ($data as $row) {
                $this->dataPreloadIndexResource->addCommitCallback(function () use ($row, &$dataIds) {
                    $this->dataPreloadIndexResource->getConnection()->insert(
                        $this->dataPreloadIndexResource->getMainTable(),
                        $row
                    );
                    $dataIds[] = $this->dataPreloadIndexResource->getConnection()->lastInsertId(
                        $this->dataPreloadIndexResource->getMainTable()
                    );
                });
            }

            $this->dataPreloadIndexResource->getConnection()->commit();
        } catch (\Exception $e) {
            $this->dataPreloadIndexResource->getConnection()->rollBack();
            throw $e;
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
                ]
            );
        }

    }
}
