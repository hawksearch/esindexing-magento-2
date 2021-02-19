<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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


namespace HawkSearch\EsIndexing\Model\Indexing;


use Magento\Framework\Exception\NoSuchEntityException;

class EntityIndexerPool implements EntityIndexerPoolInterface
{
    /**
     * @var EntityIndexerInterface[]
     */
    private $indexers = [];

    /**
     * EntityIndexerPool constructor.
     * @param array $indexers
     */
    public function __construct(
        $indexers = []
    ) {
        $this->indexers = $indexers;
    }

    /**
     * @inheritDoc
     */
    public function getIndexerByCode($code)
    {
        if ($code) {
            $indexers = $this->getIndexerList();
            if (isset($indexers[$code])) {
                return $indexers[$code];
            }
        }

        throw new NoSuchEntityException(__('Unknown Entity Indexer %1', $code));
    }

    /**
     * @inheritDoc
     */
    public function getIndexerList()
    {
        return $this->indexers;
    }
}
