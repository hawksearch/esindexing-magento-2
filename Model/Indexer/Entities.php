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

use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionFull as Action;
use Magento\Framework\Indexer\ActionInterface;

class Entities implements ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'hawksearch_entities';

    /**
     * @var Action
     */
    private Action $action;

    public function __construct(
        Action $action
    ) {
        $this->action = $action;
    }

    /**
     * @inheritdoc
     */
    public function executeFull()
    {
        $this->action->execute();
    }

    /**
     * This indexer is not designed to run partial index updates
     */
    public function executeList(array $ids)
    {
    }

    /**
     * This indexer is not designed to run partial index updates
     */
    public function executeRow($id)
    {
    }
}
