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

namespace HawkSearch\EsIndexing\Plugin\Mview\View;

use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Ddl\TriggerFactory;
use Magento\Framework\Mview\View\Subscription;

class StagingSubscriptionPlugin
{
    /**
     * @var array<string, list<string>>
     */
    private array $allowedViewTables = [
        'hawksearch_products' => [
            'catalog_product_relation'
        ]

    ];
    private TriggerFactory $triggerFactory;

    public function __construct(
        TriggerFactory $triggerFactory
    )
    {
        $this->triggerFactory = $triggerFactory;
    }

    /**
     * @return array|null
     * @throws \Zend_Db_Exception
     */
    public function beforeSaveTrigger(Subscription $subject, Trigger $trigger)
    {
        if (!array_key_exists($subject->getView()->getId(), $this->allowedViewTables)) {
            return null;
        }

        if (!in_array($subject->getTableName(), $this->allowedViewTables[$subject->getView()->getId()])) {
            return null;
        }

        if (!($subject instanceof \Magento\CatalogStaging\Model\Mview\View\Attribute\Subscription)) {
            return null;
        }

        $linkId = $subject->getColumnName();
        $newTrigger = $this->triggerFactory->create()
            ->setName($trigger->getName())
            ->setTime($trigger->getTime())
            ->setEvent($trigger->getEvent())
            ->setTable($trigger->getTable());
        foreach ($trigger->getStatements() as $statement) {
            $statement = preg_replace('/(NEW|OLD)\.`row_id`/', "$1.`$linkId`", $statement);
            $newTrigger->addStatement($statement);
        }

        return [$newTrigger];
    }
}
