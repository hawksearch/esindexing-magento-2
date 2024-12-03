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

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext as FulltextResource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * @api
 * @since 0.8.0
 */
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
     * @var ProductResource
     */
    private ProductResource $productResource;

    /**
     * @var MetadataPool
     */
    private MetadataPool $metadataPool;

    /**
     * Product constructor.
     *
     * @param ProductFactory $productFactory
     * @param Type $productType
     * @param FulltextResource $fulltextResource
     * @param ProductResource|null $productResource
     * @param MetadataPool|null $metadataPool
     */
    public function __construct(
        ProductFactory $productFactory,
        Type $productType,
        FulltextResource $fulltextResource,
        ProductResource $productResource = null,
        MetadataPool $metadataPool = null

    ) {
        $this->productFactory = $productFactory;
        $this->productType = $productType;
        $this->fulltextResource = $fulltextResource;
        $this->productResource = $productResource ?: ObjectManager::getInstance()->get(ProductResource::class);
        $this->metadataPool = $metadataPool ?: ObjectManager::getInstance()->get(MetadataPool::class);
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
     * @param int[] $ids
     * @return array
     * @throws Exception
     */
    public function getParentProductIds(array $ids): array
    {
        $parentsMap = $this->getParentsByChildMap($ids);
        $parentIds = [];
        foreach ($ids as $childId) {
            $parentIds = array_merge($parentIds, $parentsMap[$childId] ?? []);
        }

        return $parentIds;
    }

    /**
     * Get IDs of parent products by their child IDs.
     *
     * Returns a hash array where key is a child ID and values are identifiers of parent products
     * from the catalog_product_relation table.
     *
     * @param int[] $childIds
     * @return array
     * @throws Exception
     */
    public function getParentsByChildMap(array $childIds): array
    {
        $connection = $this->productResource->getConnection();
        /** @var EntityMetadataInterface $metadata */
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();

        $select = $connection->select()->from(
            ['relation' => $this->productResource->getTable('catalog_product_relation')],
            ['relation.child_id']
        )->join(
            ['e' => $this->productResource->getTable('catalog_product_entity')],
            'e.' . $linkField . ' = relation.parent_id',
            ['e.entity_id']
        )->where(
            'relation.child_id IN(?)',
            $childIds
        );

        $rows = $connection->fetchAll($select);

        $map = [];
        foreach ($rows as $row) {
            $map[$row['child_id']] = $map[$row['child_id']] ?? [];
            $map[$row['child_id']][] = $row['entity_id'];
        }

        return $map;
    }

    /**
     * Get IDs of children products by their parent IDs.
     *
     * Returns a hash array where key is a parent ID and values are identifiers of child products
     * from the catalog_product_relation table.
     *
     * @param int[] $parentIds
     * @return array
     * @throws Exception
     */
    public function getChildrenByParentMap(array $parentIds): array
    {
        $connection = $this->productResource->getConnection();
        /** @var EntityMetadataInterface $metadata */
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();

        $select = $connection->select()->from(
            ['relation' => $this->productResource->getTable('catalog_product_relation')],
            ['relation.child_id']
        )->join(
            ['e' => $this->productResource->getTable('catalog_product_entity')],
            'e.' . $linkField . ' = relation.parent_id',
            ['e.entity_id']
        )->where(
            'e.entity_id IN(?)',
            $parentIds
        );

        $rows = $connection->fetchAll($select);

        $map = [];
        foreach ($rows as $row) {
            $map[$row['entity_id']] = $map[$row['entity_id']] ?? [];
            $map[$row['entity_id']][] = $row['child_id'];
        }

        return $map;
    }
}
