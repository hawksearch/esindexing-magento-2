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
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class OnCartRemoveTrackingEventPlugin
{
    private float $qty = 0;
    private DataStorageInterface $cartItemsToRemoveDataStorage;
    private EventTrackingConfig $eventTrackingConfig;

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
     * @noinspection PhpMissingParamTypeInspection
     */
    public function beforeUpdateItem(Quote $subject, $itemId): void
    {
        $item = $subject->getItemById($itemId);
        $this->qty = $item ? $item->getQty() : 0;
    }

    /**
     * @param Quote $subject
     * @param QuoteItem $result
     * @param int $itemId
     * @return QuoteItem
     * @throws \Magento\Framework\Exception\RuntimeException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function afterUpdateItem(Quote $subject, QuoteItem $result, $itemId): QuoteItem
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
     * @noinspection PhpMissingParamTypeInspection
     */
    public function afterRemoveItem(Quote $subject, Quote $result, $itemId): Quote
    {
        $item = $subject->getItemById($itemId);
        if ($item instanceof QuoteItem) {
            $this->addItemToTriggerList($item, $item->getQty() - 0);
        }

        return $result;
    }

    /**
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    private function addItemToTriggerList(QuoteItem $resultItem, float $qty): void
    {
        if ($this->eventTrackingConfig->isEnabled()) {
            $this->cartItemsToRemoveDataStorage->reset();
            $clonnedItem = clone $resultItem;
            $clonnedItem->setQty($qty);
            $this->cartItemsToRemoveDataStorage->set([$clonnedItem]);
        }
    }

}
