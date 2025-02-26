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
    const INDEXER_ID = 'hawksearch_products';

    private ProductDataProvider $productDataProvider;
    private Action $action;
    private ConsoleOutput $output;

    public function __construct(
        ProductDataProvider $productDataProvider,
        Action $action,
        ConsoleOutput $output
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->action = $action;
        $this->output = $output;
    }

    /**
     * This indexer is not designed to run full reindex
     *
     * @return void
     * @see Entities
     */
    public function executeFull()
    {
        $phrase = __(
            "Indexer %1 can't be run for full reindexing. Please run %2 indexer instead.",
            self::INDEXER_ID,
            EntitiesIndexer::INDEXER_ID
        );
        $this->output->writeln('<comment>' . $phrase . '</comment>');
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        $ids = array_merge($ids, $this->productDataProvider->getParentProductIds($ids));

        $this->action->execute($ids);
    }
}
