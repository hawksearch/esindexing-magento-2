<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Layout;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class VueAdditionalParametersProcessor implements LayoutConfigProcessorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * VueAdditionalParametersProcessor constructor.
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request,
        UrlFinderInterface $urlFinder
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @inheritDoc
     */
    public function process($jsConfig)
    {
        $jsConfig = $jsConfig ?? [];
        if ($category = $this->getCategoryPage()) {
            $jsConfig['CustomUrl'] = $this->getCategoryPath($category);
        }

        return $jsConfig;
    }

    /**
     * @param Category $category
     * @return string|null
     */
    protected function getCategoryPath(Category $category)
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

    /**
     * Check if current page is category page
     * @return Category|null
     */
    protected function getCategoryPage()
    {
        if ($this->request->getControllerName() !== 'category') {
            return null;
        }

        /** @var Category $category */
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return null;
        }

        return $category;
    }
}
