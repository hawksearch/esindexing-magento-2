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

namespace HawkSearch\EsIndexing\Model\Indexer;

use HawkSearch\EsIndexing\Model\Indexer\Entities as EntitiesIndexer;
use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract as Action;
use HawkSearch\EsIndexing\Model\Product as ProductDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Product implements IndexerActionInterface, MviewActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'hawksearch_products';

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var Action
     */
    private $action;

    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * Product constructor.
     *
     * @param ProductDataProvider $productDataProvider
     * @param Action $action
     * @param ConsoleOutput $output
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        Action $action,
        ConsoleOutput $output
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->output = $output;
        $this->action = $action;
    }

    /**
     * This indexer is not designed to run full reindex
     *
     * @see Entities
     * @inheritDoc
     */
    public function executeFull()
    {
        $this->output->writeln(
            sprintf(
                '<comment>Indexer `%s` can\'t be run for full reindexing. Please run `%s` indexer instead.</comment>',
                self::INDEXER_ID,
                EntitiesIndexer::INDEXER_ID
            )
        );
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        $ids = array_merge($ids, $this->productDataProvider->getParentProductIds($ids));

        $this->action->execute($ids);
    }
}
