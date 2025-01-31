<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ContentPage implements IndexerActionInterface, MviewActionInterface
{
    const INDEXER_ID = 'hawksearch_content_pages';

    private Action $action;
    private ConsoleOutput $output;

    public function __construct(
        Action $action,
        ConsoleOutput $output
    ) {
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
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @return void
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute($ids)
    {
        $this->action->execute($ids);
    }
}
