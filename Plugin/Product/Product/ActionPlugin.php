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

namespace HawkSearch\EsIndexing\Plugin\Product\Product;

use HawkSearch\EsIndexing\Plugin\Product\AbstractPlugin;
use Magento\Catalog\Model\Product\Action as ProductAction;

class ActionPlugin extends AbstractPlugin
{
    /**
     * Reindex on product attribute mass change
     *
     * @param ProductAction $subject
     * @param ProductAction $action
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return ProductAction
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdateAttributes(
        ProductAction $subject,
        ProductAction $action,
        $productIds,
        $attrData,
        $storeId
    ) {
        $this->reindexList(array_unique($productIds));

        return $action;
    }

    /**
     * Reindex on product websites mass change
     *
     * @param ProductAction $subject
     * @param null $result
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdateWebsites(ProductAction $subject, $result, $productIds, $websiteIds, $type)
    {
        $this->reindexList(array_unique($productIds));
    }
}
