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

namespace HawkSearch\EsIndexing\Model\Indexing;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;

class HierarchyEntityIndexer extends AbstractEntityIndexer
{

    /**
     * @inheritDoc
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        return $item->getData($attribute);
    }

    /**
     * @param CategoryInterface|Category|DataObject $item
     * @inheritDoc
     */
    protected function canItemBeIndexed(DataObject $item)
    {
        if (!$item->getIsActive()) {
            return false;
        }
        //@TODO Check if parent categories are active

        return true;
    }

    /**
     * @param CategoryInterface|Category|DataObject $entityItem
     * @inheritDoc
     */
    protected function getEntityId($entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @return array
     */
    protected function getIndexedAttributes()
    {
        return [
            'title',
            'content_heading',
            'content'
        ];
    }
}
