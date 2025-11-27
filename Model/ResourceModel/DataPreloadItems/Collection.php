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

namespace HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems;

use HawkSearch\EsIndexing\Model\DataPreloadItems;
use HawkSearch\EsIndexing\Model\ResourceModel\DataPreloadItems as DataPreloadItemsResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            DataPreloadItems::class,
            DataPreloadItemsResource::class
        );
        $this->setMainTable(DataPreloadItemsResource::TABLE_NAME);
        $this->_setIdFieldName(DataPreloadItemsResource::TABLE_PRIMARY_KEY);
    }

    /**
     * @return array<int, DataPreloadItems>
     */
    public function saveAllNew(): array
    {
        $oldIsLoaded = $this->isLoaded();
        $this->_setIsLoaded(true);

        $newItems = [];
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();
            /** @var DataPreloadItems $item */
            foreach ($this->getItems() as $item) {
                if ($item->getId()) {
                    continue;
                }
                $this->getResource()->save($item);
                $newItems[(int)$item->getId()] = $item;
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
        $this->_setIsLoaded($oldIsLoaded);

        return $newItems;
    }
}
