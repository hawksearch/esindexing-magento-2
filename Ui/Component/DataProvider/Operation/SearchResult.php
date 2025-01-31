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

namespace HawkSearch\EsIndexing\Ui\Component\DataProvider\Operation;

use Magento\AsynchronousOperations\Ui\Component\DataProvider\Bulk\IdentifierResolver;
use Magento\AsynchronousOperations\Ui\Component\DataProvider\Operation\Failed\SearchResult as SearchResultParent;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Psr\Log\LoggerInterface as Logger;

class SearchResult extends SearchResultParent
{
    private IdentifierResolver $identifierResolver;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param IdentifierResolver $identifierResolver
     * @param JsonHelper $serializerHelper
     * @param string $mainTable
     * @param class-string $resourceModel
     * @param string $identifierName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        IdentifierResolver $identifierResolver,
        JsonHelper $serializerHelper,
        $mainTable = 'magento_operation',
        $resourceModel = null,
        $identifierName = 'id'
    ) {
        $this->identifierResolver = $identifierResolver;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $identifierResolver,
            $serializerHelper,
            $mainTable,
            $resourceModel,
            $identifierName
        );
    }

    /**
     * @return void
     */
    protected function _initSelect()
    {
        $bulkUuid = $this->identifierResolver->execute();
        $this->getSelect()
            ->reset(Select::COLUMNS)
            ->reset(Select::WHERE)
            ->from(['main_table' => $this->getMainTable()])
            ->where('bulk_uuid=?', $bulkUuid);
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            if ($item['status'] == OperationInterface::STATUS_TYPE_COMPLETE) {
                $item['result_message'] = '';
            }
        }
        return $this;
    }
}
