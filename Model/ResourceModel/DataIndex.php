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

namespace HawkSearch\EsIndexing\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class DataIndex extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public const TABLE_NAME = "hawksearch_data_index";
    public const TABLE_PRIMARY_KEY = "id";

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }

    public function incrementStage2Scheduled(AbstractModel $object, int $increment = 1): void
    {
        try {
            $this->getConnection()->beginTransaction();
            $this->incrementColumnValue((int)$object->getId(), 'stage_2_scheduled', $increment);
            $this->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }

    public function incrementStage2Completed(AbstractModel $object, int $increment = 1): void
    {
        try {
            $this->getConnection()->beginTransaction();
            $this->incrementColumnValue((int)$object->getId(), 'stage_2_completed', $increment);
            $this->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }

    private function incrementColumnValue(int $rowId, string $columnName, int $increment = 1): void
    {
        $condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $rowId);
        $expr = sprintf('%s + %d', $this->getConnection()->quoteIdentifier($columnName), $increment);
        $bind = [
            $columnName => new \Zend_Db_Expr($expr)
        ];

        $this->getConnection()->update($this->getMainTable(), $bind, $condition);
    }
}
