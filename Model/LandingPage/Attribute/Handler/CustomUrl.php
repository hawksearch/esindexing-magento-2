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

namespace HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler;

use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\DataObject;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class CustomUrl implements AttributeHandlerInterface
{
    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * CustomUrl constructor.
     *
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        UrlFinderInterface $urlFinder
    ) {
        $this->urlFinder = $urlFinder;
    }

    /**
     * @inheritDoc
     * @todo implement handler which can get other attribute values or has access to the final entity
     * @param CategoryInterface $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        return sprintf("%s", $this->getRequestPath($item));
    }

    /**
     * @param Category|CategoryInterface $category
     * @return string|null
     */
    protected function getRequestPath(Category $category)
    {
        if ($category->hasData('request_path') && $category->getRequestPath() != null) {
            return $category->getRequestPath();
        }
        $rewrite = $this->urlFinder->findOneByData(
            [
                UrlRewrite::ENTITY_ID => $category->getId(),
                UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID => $category->getStoreId(),
            ]
        );
        if ($rewrite) {
            return $rewrite->getRequestPath();
        }
        return null;
    }
}
