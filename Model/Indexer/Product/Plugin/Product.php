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

namespace HawkSearch\EsIndexing\Model\Indexer\Product\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Model\AbstractModel;

class Product extends AbstractPlugin
{
    /**
     * Reindex on product save.
     *
     * @param ProductResource $productResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return ProductResource
     * @throws \Exception
     */
    public function aroundSave(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex on product delete
     *
     * @param ProductResource $productResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return ProductResource
     * @throws \Exception
     */
    public function aroundDelete(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        return $this->addCommitCallback($productResource, $proceed, $object);
    }

    /**
     * Reindex catalog search.
     *
     * @param ProductResource $productResource
     * @param \Closure $proceed
     * @param AbstractModel $object
     * @return ProductResource
     * @throws \Exception
     */
    private function addCommitCallback(ProductResource $productResource, \Closure $proceed, AbstractModel $object)
    {
        try {
            $productResource->beginTransaction();
            $result = $proceed($object);
            $productResource->addCommitCallback(function () use ($object) {
                $this->reindexRow($object->getEntityId());
            });
            $productResource->commit();
        } catch (\Exception $e) {
            $productResource->rollBack();
            throw $e;
        }

        return $result;
    }
}
