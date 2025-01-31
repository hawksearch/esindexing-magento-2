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

namespace HawkSearch\EsIndexing\Ui\Component\DataProvider\BulkListing;

use HawkSearch\EsIndexing\Model\BulkOperation\BulkOperationManagement;
use HawkSearch\EsIndexing\Model\ResourceModel\AsynchronousOperations\Operation as OperationResource;
use Magento\AsynchronousOperations\Model\BulkStatus\CalculatedStatusSql;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory as OperationCollectionFactory;
use Magento\AsynchronousOperations\Model\StatusMapper;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Psr\Log\LoggerInterface as Logger;

class SearchResult extends \Magento\AsynchronousOperations\Ui\Component\DataProvider\SearchResult
{

    private OperationCollectionFactory $operationCollectionFactory;
    private OperationResource $operationResource;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param UserContextInterface $userContextInterface
     * @param StatusMapper $statusMapper
     * @param CalculatedStatusSql $calculatedStatusSql
     * @param OperationCollectionFactory $operationCollectionFactory
     * @param OperationResource $operationResourceConfig
     * @param string $mainTable
     * @param AbstractResource $resourceModel
     * @param string $identifierName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @noinspection PhpMissingParamTypeInspection
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        UserContextInterface $userContextInterface,
        StatusMapper $statusMapper,
        CalculatedStatusSql $calculatedStatusSql,
        OperationCollectionFactory $operationCollectionFactory,
        OperationResource $operationResourceConfig,
        $mainTable = 'magento_bulk',
        $resourceModel = null,
        $identifierName = 'uuid'
    ) {
        $this->operationCollectionFactory = $operationCollectionFactory;
        $this->operationResource = $operationResourceConfig;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $userContextInterface,
            $statusMapper,
            $calculatedStatusSql,
            $mainTable,
            $resourceModel,
            $identifierName
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->reset(Select::WHERE)
            ->joinInner(
                ['op' => $this->getOperationsSubQuery()],
                'op.bulk_uuid = main_table.uuid'
            )->columns(
                [
                    'status_summary' => new \Zend_Db_Expr(
                        'CONCAT(op.status_open," / ",op.status_complete," / ",op.status_failed)'
                    )
                ]
            )->where("op.all_count = op.allowed_topic_count");
        return $this;
    }

    /**
     * Get sql to get data from `magento_operation` table
     *
     * @return Select
     */
    protected function getOperationsSubQuery()
    {
        $collection = $this->operationCollectionFactory->create();
        $columns = [
            'bulk_uuid',
            'is_fullreindex' => new \Zend_Db_Expr(
                'MAX(
                            IF(
                                topic_name LIKE "hawksearch.indexing.fullreindex.start",
                                1,
                                0
                            )
                        )'
            ),
            'status_complete' => new \Zend_Db_Expr(
                'COUNT(
                            IF(
                                status = ' . OperationInterface::STATUS_TYPE_COMPLETE . ',
                                1,
                                NULL
                            )
                        )'
            ),
            'status_failed' => new \Zend_Db_Expr(
                'COUNT(
                            IF(
                                status IN (' .
                implode(',', [
                    OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                    OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED,
                    OperationInterface::STATUS_TYPE_REJECTED,
                ])
                . '),
                                1,
                                NULL
                            )
                        )'
            ),
            'status_open' => new \Zend_Db_Expr(
                'COUNT(
                            IF(
                                status = ' . OperationInterface::STATUS_TYPE_OPEN . ',
                                1,
                                NULL
                            )
                        )'
            ),
            'all_count' => new \Zend_Db_Expr('COUNT(*)'),
            'allowed_topic_count' => new \Zend_Db_Expr(
                'COUNT(
                            IF(
                                topic_name LIKE "' . BulkOperationManagement::OPERATION_TOPIC_PREFIX . '%",
                                1,
                                NULL
                            )
                        )'
            ),
        ];

        if ($this->operationResource->isStartedAtColumnExists()) {
            $columns['last_time'] = new \Zend_Db_Expr('MAX(started_at)');
        }

        return $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns($columns)
            ->group('bulk_uuid');
    }
}
