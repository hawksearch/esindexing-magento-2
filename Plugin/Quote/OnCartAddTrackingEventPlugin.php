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
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\RuntimeException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class OnCartAddTrackingEventPlugin
{
    /**
     * @var int|float
     */
    private $qty;

    /**
     * @var bool
     */
    private $isSkipAddNewItem;

    /**
     * @var DataStorageInterface
     */
    private $cartItemsToAddDataStorage;

    /**
     * @var EventTrackingConfig
     */
    private $eventTrackingConfig;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param DataStorageInterface $cartItemsToAddDataStorage
     * @param EventTrackingConfig $eventTrackingConfig
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        DataStorageInterface $cartItemsToAddDataStorage,
        EventTrackingConfig $eventTrackingConfig,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->cartItemsToAddDataStorage = $cartItemsToAddDataStorage;
        $this->eventTrackingConfig = $eventTrackingConfig;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Initialize cart item qty before updating the item
     *
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
        $this->isSkipAddNewItem = true;

        return null;
    }

    /**
     * @param Quote $subject
     * @param QuoteItem $result
     * @param int $itemId
     * @param DataObject $buyRequest
     * @param null|array|DataObject $params
     * @return QuoteItem
     * @throws RuntimeException
     */
    public function afterUpdateItem(Quote $subject, $result, $itemId, $buyRequest, $params = null)
    {
        $this->isSkipAddNewItem = false;
        if ($this->qty > $result->getQty()) {
            return $result;
        }

        $this->addItemToTriggerList($result);

        return $result;
    }

    /**
     * Initialize cart item qty before adding product
     *
     * @param Quote $subject
     * @param Product $product
     * @param float|DataObject|null $request
     * @param string|null $processMode
     * @return null
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if ($this->isSkipAddNewItem) {
            return null;
        }

        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = $this->dataObjectFactory->create(['qty' => $request]);
        }
        if (!$request instanceof \Magento\Framework\DataObject) {
            return null;
        }

        $this->qty = $request->getData('qty') ?: 0;

        return null;
    }

    /**
     * @param Quote $subject
     * @param QuoteItem|string $result
     * @param Product|mixed $product
     * @param float|\Magento\Framework\DataObject|null $request
     * @param string|null $processMode
     * @return QuoteItem|string
     * @throws RuntimeException
     */
    public function afterAddProduct(
        Quote $subject,
        $result,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if ($this->isSkipAddNewItem) {
            return $result;
        }

        // Error
        if (is_string($result)) {
            return $result;
        }

        if ($this->qty > $result->getQty()) {
            return $result;
        }

        //A new product is added to cart
        if ($this->qty === $result->getQty() && $this->qty != 0) {
            $this->qty = 0;
        }

        $this->addItemToTriggerList($result);

        return $result;
    }

    /**
     * @param QuoteItem $item
     * @return void
     * @throws RuntimeException
     */
    private function addItemToTriggerList($item)
    {
        if ($this->eventTrackingConfig->isEnabled()) {
            $this->cartItemsToAddDataStorage->reset();
            $clonnedItem = clone $item;
            $clonnedItem->setQty($item->getQty() - $this->qty);
            $this->cartItemsToAddDataStorage->set([$clonnedItem]);
        }
    }
}
