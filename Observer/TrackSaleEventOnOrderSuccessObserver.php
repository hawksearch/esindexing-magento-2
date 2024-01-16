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

namespace HawkSearch\EsIndexing\Observer;

use HawkSearch\EsIndexing\Block\Tracking as TrackingBlock;
use HawkSearch\EsIndexing\Model\Config\EventTracking;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class TrackSaleEventOnOrderSuccessObserver implements ObserverInterface
{
    /**
     * @var EventTracking
     */
    private $eventTrackingConfig;

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        EventTracking $eventTrackingConfig,
        LayoutInterface $layout
    ) {
        $this->eventTrackingConfig = $eventTrackingConfig;
        $this->layout = $layout;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->eventTrackingConfig->isEnabled()) {
            return;
        }

        $orderIds = (array)$observer->getEvent()->getData('order_ids');

        /** @var TrackingBlock $block */
        $block = $this->layout->getBlock('hawksearch.esindexing.eventtracking');
        if ($block) {
            $block->setData('order_ids', $orderIds);
        }
    }
}
