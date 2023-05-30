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

namespace HawkSearch\EsIndexing\Model\Mview\View;

use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\Mview\ViewInterface;

class StagingSubscription extends \Magento\CatalogStaging\Model\Mview\View\Attribute\Subscription
{

    /**
     * @inheritDoc
     */
    protected function buildStatement(string $event, ViewInterface $view): string
    {
        $triggerBody = parent::buildStatement($event, $view);
        $bodyParts = explode(PHP_EOL, $triggerBody);
        $preStatement = array_shift($bodyParts);
        $stringCondition = 'SET @entity_id';
        $preStatement = (substr((string)$preStatement, 0, strlen($stringCondition)) == $stringCondition)
            ? $preStatement
            : '';

        if (!$preStatement) {
            return $preStatement;
        }

        switch ($event) {
            case Trigger::EVENT_INSERT:
            case Trigger::EVENT_UPDATE:
                $eventType = 'NEW';
                break;
            case Trigger::EVENT_DELETE:
                $eventType = 'OLD';
                break;
            default:
                return $triggerBody;
        }

        $preStatement = $this->buildEntityIdStatementByEventType($eventType, $view);

        return $preStatement . implode(PHP_EOL, $bodyParts);
    }

    /**
     * Build trigger body
     *
     * @param string $eventType
     * @param ViewInterface $view
     * @return string
     */
    private function buildEntityIdStatementByEventType(string $eventType, ViewInterface $view): string
    {
        return vsprintf(
                'SET @entity_id = (SELECT %1$s FROM %2$s WHERE %3$s = %4$s.%5$s);',
                [
                    $this->connection->quoteIdentifier(
                        $this->entityMetadata->getIdentifierField()
                    ),
                    $this->connection->quoteIdentifier(
                        $this->resource->getTableName($this->entityMetadata->getEntityTable())
                    ),
                    $this->connection->quoteIdentifier(
                        $this->entityMetadata->getLinkField()
                    ),
                    $eventType,
                    $this->connection->quoteIdentifier($this->getSubscriptionColumn($view))
                ]
            ) . PHP_EOL;
    }

    /**
     * Returns subscription column name by view
     *
     * @param ViewInterface $view
     * @return string
     */
    private function getSubscriptionColumn(ViewInterface $view): string
    {
        $subscriptions = $view->getSubscriptions();
        if (!isset($subscriptions[$this->getTableName()]['column'])) {
            throw new \RuntimeException(sprintf('Column name for view with id "%s" doesn\'t exist', $view->getId()));
        }

        return $subscriptions[$this->getTableName()]['column'];
    }
}
