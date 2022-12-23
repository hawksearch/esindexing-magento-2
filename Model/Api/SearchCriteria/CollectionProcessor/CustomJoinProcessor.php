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
namespace HawkSearch\EsIndexing\Model\Api\SearchCriteria\CollectionProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Search criteria custom join processor
 */
class CustomJoinProcessor implements CollectionProcessorInterface
{
    /**
     * @var CustomJoinInterface[]
     */
    private $joins;

    /**
     * @var array
     */
    private $appliedJoins = [];

    /**
     * @param CustomJoinInterface[] $customJoins
     */
    public function __construct(
        array $customJoins = []
    ) {
        $this->joins = $customJoins;
    }

    /**
     * Apply Search Criteria Filters to collection only if we need this
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     * @return void
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        foreach ($this->joins as $joinName => $joinType) {
            if (isset($this->appliedJoins[$joinName])) {
                continue;
            }
            $this->applyCustomJoin($joinName, $collection);
        }
    }

    /**
     * Apply join to collection
     *
     * @param string $joinName
     * @param AbstractDb $collection
     * @return void
     */
    private function applyCustomJoin($joinName, AbstractDb $collection)
    {
        $customJoin = $this->getCustomJoin($joinName);

        if ($customJoin) {
            $customJoin->apply($collection);
        }
    }

    /**
     * Return custom filters for field if exists
     *
     * @param string $joinName
     * @return CustomJoinInterface|null
     * @throws \InvalidArgumentException
     */
    private function getCustomJoin($joinName)
    {
        $joinType = null;
        if (isset($this->joins[$joinName])) {
            $joinType = $this->joins[$joinName];
            if (!($this->joins[$joinName] instanceof CustomJoinInterface)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Custom join for %s must implement %s interface.',
                        $joinName,
                        CustomJoinInterface::class
                    )
                );
            }
        }
        return $joinType;
    }
}
