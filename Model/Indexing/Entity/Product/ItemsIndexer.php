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
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
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

    public function __construct(
        ArrayToJsonConverter $converter,
        DataPreloadIndex $dataPreloadIndexResource
    ) {
        $this->converter = $converter;
        $this->dataPreloadIndexResource = $dataPreloadIndexResource;
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

        $insertBathesLimit = 100;
        $insertData = [];

        for ($page = 1; $page <= $itemsBatches; $page++) {
            $insertData[] = [
                'method' => $method,
                'request' => $this->converter->convert([
                    'IndexName' => $this->indexName,
                    $dataFieldsMap[$method] => $itemsChunks[$page - 1]
                ])
            ];
            if ($page / $insertBathesLimit >= 1) {
                $this->saveMultipleRows($insertData);
                $insertData = [];
            }
        }

        if ($insertData) {
            $this->saveMultipleRows($insertData);
        }
    }

    /**
     * @param list<array<string, mixed>> $data
     * @return void
     */
    private function saveMultipleRows(array $data): void
    {
        $this->dataPreloadIndexResource->getConnection()->insertMultiple($this->dataPreloadIndexResource->getMainTable(), $data);
    }
}
