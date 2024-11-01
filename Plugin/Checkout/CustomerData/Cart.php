<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\Cart as CustomerDataCart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class Cart
{
    /**
     * @var Quote|null
     */
    protected $quote = null;

    private CheckoutSession $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add extra price data to result
     *
     * @param CustomerDataCart $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(CustomerDataCart $subject, array $result)
    {
        $items = $this->getQuote()->getAllVisibleItems();
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
                if ($item = $this->findItemById((int)$itemAsArray['item_id'], $items)) {
                    $result['items'][$key]['product_price_incl_tax'] = $item->getPriceInclTax();
                }
            }
        }

        return $result;
    }

    /**
     * Get active quote
     *
     * @return Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getQuote(): ?Quote
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * Find item by id in items haystack
     *
     * @param int $id
     * @param array $itemsHaystack
     * @return QuoteItem | bool
     */
    protected function findItemById(int $id, array $itemsHaystack)
    {
        foreach ($itemsHaystack as $item) {
            /** @var $item QuoteItem */
            if ((int)$item->getItemId() === $id) {
                return $item;
            }
        }

        return false;
    }
}
