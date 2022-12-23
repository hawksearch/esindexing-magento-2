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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class EntityPriceObserver implements ObserverInterface
{
    /**
     * @var PriceManagementInterface
     */
    private $priceManagement;

    /**
     * EntityPriceObserver constructor.
     * @param PriceManagementInterface $priceManagement
     */
    public function __construct(
        PriceManagementInterface $priceManagement
    ) {
        $this->priceManagement = $priceManagement;
    }

    /**
     * Add product entity pricing data to the index data
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $itemData */
        $itemData = $observer->getData('item_data');
        /** @var ProductInterface|ProductModel $product */
        $product = $observer->getData('item');

        if (!$product instanceof ProductInterface) {
            return $this;
        }

        $priceInfo = [];
        $this->priceManagement->collectPrices($product, $priceInfo);
        $itemData->addData($priceInfo);

        return $this;
    }
}
