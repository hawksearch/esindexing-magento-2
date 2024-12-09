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

use HawkSearch\EsIndexing\Model\Config\EventTracking as EventTrackingConfig;
use HawkSearch\EsIndexing\Model\Indexing\EntityType\ProductEntityType;
use HawkSearch\EsIndexing\Service\DataStorageInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class SendCookieOnCartCompleteObserver implements ObserverInterface
{
    /**
     * @var PublicCookieMetadata
     */
    private $cookieMetadata;

    /**
     * @var DataStorageInterface
     */
    private $cartItemsToAddDataStorage;

    /**
     * @var DataStorageInterface
     */
    private $cartItemsToRemoveDataStorage;

    /**
     * @var EventTrackingConfig
     */
    private $eventTrackingConfig;

    /**
     * @var ProductEntityType
     */
    private $productEntityType;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    public function __construct(
        DataStorageInterface $cartItemsToAddDataStorage,
        DataStorageInterface $cartItemsToRemoveDataStorage,
        EventTrackingConfig $eventTrackingConfig,
        ProductEntityType $productEntityType,
        CookieMetadataFactory $cookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        RequestInterface $httpRequest,
        SerializerInterface $jsonSerializer
    ) {
        $this->cartItemsToAddDataStorage = $cartItemsToAddDataStorage;
        $this->cartItemsToRemoveDataStorage = $cartItemsToRemoveDataStorage;
        $this->eventTrackingConfig = $eventTrackingConfig;
        $this->productEntityType = $productEntityType;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->httpRequest = $httpRequest;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->eventTrackingConfig->isEnabled() || $this->httpRequest->isXmlHttpRequest()) {
            return;
        }

        $this->setCookieForCartItems(
            EventTrackingConfig::COOKIE_ADD_TO_CART_NAME,
            (array)$this->cartItemsToAddDataStorage->get(true)
        );
        $this->setCookieForCartItems(
            EventTrackingConfig::COOKIE_REMOVE_FROM_CART_NAME,
            (array)$this->cartItemsToRemoveDataStorage->get(true)
        );
    }

    /**
     * @return array
     */
    protected function formatCartItem(QuoteItem $item)
    {
        return [
            'uniqueId' => $this->productEntityType->getUniqueId($item->getProductId()),
            'itemPrice' => (float)$item->getPrice(),
            'quantity' => (float)$item->getQty()
        ];
    }

    /**
     * @param string $cookieName
     * @param QuoteItem[] $cartItems
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    protected function setCookieForCartItems(string $cookieName, array $cartItems)
    {
        if (!empty($cartItems)) {
            $cartItems = array_map([$this, 'formatCartItem'], $cartItems);
            $this->cookieManager->setPublicCookie(
                $cookieName,
                rawurlencode($this->jsonSerializer->serialize($cartItems)),
                $this->getCookieMetaData()
            );
        }
    }

    /**
     * @return PublicCookieMetadata
     */
    private function getCookieMetaData()
    {
        if (!isset($this->cookieMetadata)) {
            $this->cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(3600)
                ->setPath('/')
                ->setHttpOnly(false)
                ->setSameSite('Strict');
        }

        return $this->cookieMetadata;
    }
}
