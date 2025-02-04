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

use HawkSearch\EsIndexing\Model\Indexer\Product as ProductIndexer;
use HawkSearch\EsIndexing\Model\Product\Attributes;
use HawkSearch\EsIndexing\Plugin\Product\AbstractPlugin;
use Magento\Catalog\Model\ResourceModel\Attribute as AttributeResourceModel;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;

class AttributePlugin extends AbstractPlugin
{
    private bool $isInvalidationNeeded = false;
    private IndexerInterface $productIndexer;
    private Attributes $productAttributes;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        Attributes $productAttributes
    ) {
        parent::__construct($indexerRegistry);
        $this->productIndexer = $indexerRegistry->get(ProductIndexer::INDEXER_ID);
        $this->productAttributes = $productAttributes;
    }

    /**
     * Check if product indexer invalidation is needed on attribute delete
     *
     * @param AttributeResourceModel $subject
     * @param Attribute $attribute
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDelete(
        AttributeResourceModel $subject,
        AbstractModel $attribute
    ): void {
        $this->isInvalidationNeeded = $this->isIndexInvalidationNeeded($attribute);
    }

    /**
     * Invalidate product indexer on attribute delete
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        AttributeResourceModel $subject,
        AttributeResourceModel $result
    ): AttributeResourceModel {
        if ($this->isInvalidationNeeded) {
            $this->productIndexer->invalidate();
        }
        $this->isInvalidationNeeded = false;

        return $result;
    }

    /**
     * Check if product index should be invalidated
     * Do not invalidate index for new attributes
     * becasue new attributes should be added to indexed attributes list first
     *
     * @param Attribute $attribute
     */
    private function isIndexInvalidationNeeded(AbstractModel $attribute): bool
    {
        $attributes = $this->productAttributes->getIndexedAttributes();
        return !$attribute->isObjectNew() && in_array($attribute->getAttributeCode(), $attributes);
    }
}
