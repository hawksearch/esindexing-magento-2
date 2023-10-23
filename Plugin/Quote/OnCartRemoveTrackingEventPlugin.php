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

namespace HawkSearch\EsIndexing\Plugin\Quote;

use HawkSearch\EsIndexing\Model\Config\EventTracking as EventTrackingConfig;
use HawkSearch\EsIndexing\Service\DataStorageInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class OnCartRemoveTrackingEventPlugin
{
    /**
     * @var int|float
     */
    private $qty;

    /**
     * @var DataStorageInterface
     */
    private $cartItemsToRemoveDataStorage;

    /**
     * @var EventTrackingConfig
     */
    private $eventTrackingConfig;

    /**
     * @param DataStorageInterface $cartItemsToRemoveDataStorage
     * @param EventTrackingConfig $eventTrackingConfig
     */
    public function __construct(
        DataStorageInterface $cartItemsToRemoveDataStorage,
        EventTrackingConfig $eventTrackingConfig
    ) {
        $this->cartItemsToRemoveDataStorage = $cartItemsToRemoveDataStorage;
        $this->eventTrackingConfig = $eventTrackingConfig;
    }

    /**
     * @param Quote $subject
     * @param int $itemId
     * @param DataObject  $buyRequest
     * @param null|array|DataObject $params
     * @return null
     */
    public function beforeUpdateItem(Quote $subject, $itemId, $buyRequest, $params = null)
    {
        $item = $subject->getItemById($itemId);
        $this->qty = $item ? $item->getQty() : 0;

        return null;
    }

    /**
     * @param Quote $subject
     * @param QuoteItem $result
     * @param int $itemId
     * @param DataObject $buyRequest
     * @param null|array|DataObject $params
     * @return QuoteItem
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function afterUpdateItem(Quote $subject, $result, $itemId, $buyRequest, $params = null)
    {
        if ($this->qty > $result->getQty() && (int)$itemId === (int)$result->getItemId()) {
            $this->addItemToTriggerList($result, $this->qty - $result->getQty());
        }

        return $result;
    }

    /**
     * @param Quote $subject
     * @param Quote $result
     * @param int $itemId
     * @return Quote
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function afterRemoveItem(Quote $subject, $result, $itemId)
    {
        $item = $subject->getItemById($itemId);
        if ($item) {
            $this->addItemToTriggerList($item, $item->getQty());
        }

        return $result;
    }

    /**
     * @param QuoteItem $resultItem
     * @param float $qty
     * @return void
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    private function addItemToTriggerList($resultItem, $qty)
    {
        if ($this->eventTrackingConfig->isEnabled()) {
            $this->cartItemsToRemoveDataStorage->reset();
            $clonnedItem = clone $resultItem;
            $clonnedItem->setQty($qty);
            $this->cartItemsToRemoveDataStorage->set([$clonnedItem]);
        }
    }

}
