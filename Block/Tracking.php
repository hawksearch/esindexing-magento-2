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

namespace HawkSearch\EsIndexing\Block;

use HawkSearch\EsIndexing\Model\Config\EventTracking as EventTrackingConfig;
use HawkSearch\EsIndexing\Model\Indexing\EntityType\ProductEntityType;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * @api
 * @since 0.8.0
 */
class Tracking extends Template
{
    private OrderCollectionFactory $orderCollectionFactory;
    private ProductEntityType $productEntityType;
    private EventTrackingConfig $eventTrackingConfig;

    /**
     * @param Template\Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ProductEntityType $productEntityType
     * @param EventTrackingConfig $eventTrackingConfig
     * @param array<string,mixed> $data
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        Template\Context $context,
        OrderCollectionFactory $orderCollectionFactory,
        ProductEntityType $productEntityType,
        EventTrackingConfig $eventTrackingConfig,
        array $data = [],
        ?SerializerInterface $serializer = null
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productEntityType = $productEntityType;
        $this->eventTrackingConfig = $eventTrackingConfig;
        $data['serializer'] = $serializer ?? ObjectManager::getInstance()->get(SerializerInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * Is tracking available
     *
     * @return bool
     */
    protected function isAvailable()
    {
        return $this->eventTrackingConfig->isEnabled();
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get currency code for tracking scripts
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Gete orders and items data as array
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderData()
    {
        $data = [];
        $orderIds = (array)$this->getOrderIds();

        if (empty($orderIds)) {
            return $data;
        }

        $ordersCollection = $this->orderCollectionFactory->create();
        $ordersCollection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        /** @var Order $order */
        foreach ($ordersCollection as $order) {
            $items = [];
            /** @var Item $item */
            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = [
                    'uniqueId' => $this->productEntityType->getUniqueId((string)$item->getProductId()),
                    'itemPrice' => (float)$item->getBasePrice(),
                    'quantity' => (float)$item->getQtyOrdered(),
                ];
            }

            $data[] = [
                'orderNo' => $order->getIncrementId(),
                'currency' => $this->getCurrencyCode(),
                'subTotal' => (float)$order->getBaseSubtotal(),
                'tax' => (float)$order->getBaseTaxAmount(),
                'total' => (float)$order->getBaseGrandTotal(),
                'itemList' => $items,
            ];
        }

        return $data;
    }

    /**
     * Get product ID template compatible with mage/utils/template JavaScript library
     *
     * @return string
     */
    public function getProductIdTemplate()
    {
        $fakeId = 9999;
        $fakeUniqueId = $this->productEntityType->getUniqueId((string)$fakeId);
        return str_replace((string)$fakeId, '${ $.id }', $fakeUniqueId);
    }
}
