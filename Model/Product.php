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

namespace HawkSearch\EsIndexing\Model;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext as FulltextResource;

class Product
{
    /**
     * @var AbstractType[]|null
     */
    private ?array $compositeTypes = null;

    /**
     * @var AbstractType[]|null
     */
    private ?array $productAllTypes = null;

    /**
     * @var ProductFactory
     */
    private ProductFactory $productFactory;

    /**
     * @var Type
     */
    private Type $productType;

    /**
     * @var FulltextResource
     */
    private FulltextResource $fulltextResource;

    /**
     * Product constructor.
     *
     * @param ProductFactory $productFactory
     * @param Type $productType
     * @param FulltextResource $fulltextResource
     */
    public function __construct(
        ProductFactory $productFactory,
        Type $productType,
        FulltextResource $fulltextResource
    ) {
        $this->productFactory = $productFactory;
        $this->productType = $productType;
        $this->fulltextResource = $fulltextResource;
    }

    /**
     * @return AbstractType[]|null
     */
    public function getCompositeTypes(): ?array
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
     * @return AbstractType[]|null
     */
    public function getAllTypes(): ?array
    {
        if ($this->productAllTypes === null) {
            $productMock = $this->productFactory->create();
            foreach ($this->productType->getTypes() as $typeId => $typeInfo) {
                $productMock->setTypeId($typeId);
                $this->productAllTypes[$typeId] = $this->productType->factory($productMock);
            }
        }

        return $this->productAllTypes;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getParentProductIds(array $ids): array
    {
        return $this->fulltextResource->getRelationsByChild($ids);
    }
}
