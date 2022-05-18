<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product as ProductModel;

class Product
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var AbstractType[]
     */
    private $compositeTypes;

    /**
     * @var Type
     */
    private $productType;

    /**
     * Product constructor.
     * @param ProductFactory $productFactory
     * @param Type $productType
     */
    public function __construct(
        ProductFactory $productFactory,
        Type $productType
    ) {
        $this->productFactory = $productFactory;
        $this->productType = $productType;
    }

    /**
     * @return AbstractType[]|null
     */
    public function getCompositeTypes()
    {
        if ($this->compositeTypes === null) {
            $productMock = $this->productFactory->create();
            foreach ($this->productType->getCompositeTypes() as $typeId) {
                $productMock->setTypeId($typeId);
                $this->compositeTypes[$typeId] = $this->productType->factory($productMock);
            }
        }

        return $this->compositeTypes;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getParentProductIds(array $ids)
    {
        $parentIds = [];
        foreach ($this->getCompositeTypes() as $typeInstance) {
            $parentIds = array_merge($parentIds, $typeInstance->getParentIdsByChild($ids));
        }

        return $parentIds;
    }
}
