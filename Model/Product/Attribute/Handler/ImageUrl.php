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

namespace HawkSearch\EsIndexing\Model\Product\Attribute\Handler;

use HawkSearch\Connector\Helper\Url as UrlHelper;
use HawkSearch\EsIndexing\Model\Config\Advanced as AdvancedConfig;
use HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandlerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ImageUrl implements AttributeHandlerInterface
{
    protected const ATTRIBUTE_IMAGE_ID_MAP = [
        'image_url' => 'product_base_image',
        'thumbnail_url' => 'product_thumbnail_image'
    ];

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var AdvancedConfig
     */
    private $advancedConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ImageUrl constructor.
     * @param ImageHelper $imageHelper
     * @param UrlHelper $urlHelper
     * @param AdvancedConfig $advancedConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ImageHelper $imageHelper,
        UrlHelper $urlHelper,
        AdvancedConfig $advancedConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->imageHelper = $imageHelper;
        $this->urlHelper = $urlHelper;
        $this->advancedConfig = $advancedConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     * @param ProductInterface $item
     * @throws NoSuchEntityException
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $value = '';
        if (array_key_exists($attributeCode, static::ATTRIBUTE_IMAGE_ID_MAP)) {
            $value = $this->getImageIdUrl($item, static::ATTRIBUTE_IMAGE_ID_MAP[$attributeCode]);
        }

        return $value;
    }

    /**
     * Get product image URL by image_id
     * @param ProductInterface|Product $product
     * @param string $imageId
     * @return string
     * @throws NoSuchEntityException
     */
    private function getImageIdUrl(ProductInterface $product, string $imageId)
    {
        $imageUrl = $this->imageHelper->init($product, $imageId)->getUrl();
        $uri = $this->urlHelper->getUriInstance($imageUrl);

        $store = $this->storeManager->getStore($product->getStoreId());
        if ($this->advancedConfig->isRemovePubFromAssetsUrl($store)) {
            /** @link  https://github.com/magento/magento2/issues/9111 */
            $uri = $this->urlHelper->removeFromUriPath($uri, ['pub']);
        }

        return (string)$uri->withScheme('');
    }
}
