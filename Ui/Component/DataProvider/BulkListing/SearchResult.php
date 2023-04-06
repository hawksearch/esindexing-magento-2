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

use HawkSearch\EsIndexing\Model\MessageQueue\IndexingOperationValidator;
use Magento\AsynchronousOperations\Model\BulkStatus\CalculatedStatusSql;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory as OperationCollectionFactory;
use Magento\AsynchronousOperations\Model\StatusMapper;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class SearchResult extends \Magento\AsynchronousOperations\Ui\Component\DataProvider\SearchResult
{

    /**
     * @var OperationCollectionFactory
     */
    private $operationCollectionFactory;

    /**
     * SearchResult constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param UserContextInterface $userContextInterface
     * @param StatusMapper $statusMapper
     * @param CalculatedStatusSql $calculatedStatusSql
     * @param OperationCollectionFactory $operationCollectionFactory
     * @param string $mainTable
     * @param null $resourceModel
     * @param string $identifierName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        $mainTable = 'magento_bulk',
        $resourceModel = null,
        $identifierName = 'uuid'
    ) {
        $this->operationCollectionFactory = $operationCollectionFactory;
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
     * @inheritDoc
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
        return $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                [
                    'bulk_uuid',
                    'last_time' => new \Zend_Db_Expr('MAX(started_at)'),
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
                                status = '. OperationInterface::STATUS_TYPE_COMPLETE .',
                                1,
                                NULL
                            )
                        )'
                    ),
                    'status_failed' => new \Zend_Db_Expr(
                        'COUNT(
                            IF(
                                status IN ('.
                                    implode(',', [
                                        OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                                        OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED,
                                        OperationInterface::STATUS_TYPE_REJECTED,
                                    ])
                                .'),
                                1,
                                NULL
                            )
                        )'
                    ),
                    'status_open' => new \Zend_Db_Expr(
                        'COUNT(
                            IF(
                                status = '. OperationInterface::STATUS_TYPE_OPEN .',
                                1,
                                NULL
                            )
                        )'
                    ),
                    'all_count' => new \Zend_Db_Expr('COUNT(*)'),
                    'allowed_topic_count' => new \Zend_Db_Expr(
                        'COUNT(
                            IF(
                                topic_name LIKE "'. IndexingOperationValidator::OPERATION_TOPIC_PREFIX .'%",
                                1,
                                NULL
                            )
                        )'
                    ),
                ]
            )
            ->group('bulk_uuid');
    }
}
