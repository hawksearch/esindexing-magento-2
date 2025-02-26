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

namespace HawkSearch\EsIndexing\Model\Layout\ConfigProcessor\Vue;

use HawkSearch\EsIndexing\Model\Layout\LayoutConfigProcessorInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class VueAdditionalParametersProcessor implements LayoutConfigProcessorInterface
{
    private Registry $registry;
    private RequestInterface $request;
    private UrlFinderInterface $urlFinder;

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
     * @return array<string, mixed>
     */
    public function process(array $jsConfig)
    {
        $params = [];
        $queryParam = [];
        if ($category = $this->getCategoryPage()) {
            $params['CustomUrl'] = $this->getCategoryPath($category);
            $queryParam[] = 'visibility_catalog:true';
        } else {
            $queryParam[] = 'visibility_search:true';
        }

        $params['Query'] = implode(' AND ', $queryParam);

        $jsConfig['additionalParameters'] = $params;

        return $jsConfig;
    }

    /**
     * @return string|null
     */
    protected function getCategoryPath(Category $category)
    {
        $path = null;

        if ($category->hasData('request_path') && $category->getRequestPath() != null) {
            $path = $category->getRequestPath();
        }

        $rewrite = $this->urlFinder->findOneByData(
            [
                UrlRewrite::ENTITY_ID => $category->getId(),
                UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID => $category->getStoreId(),
            ]
        );
        if ($rewrite) {
            $path = $rewrite->getRequestPath();
        }

        if ($path) {

        }

        return $path;
    }

    /**
     * Check if current page is a category page an return category
     *
     * @return Category|null
     */
    protected function getCategoryPage()
    {
        if ($this->request->getControllerName() !== 'category') {
            return null;
        }

        /** @var ?Category $category */
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return null;
        }

        return $category;
    }
}
