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

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\DataObject;

class ContentPageEntityIndexer extends AbstractEntityIndexer
{

    /**
     * @inheritDoc
     */
    protected function getAttributeValue(DataObject $item, string $attribute)
    {
        return $item->getData($attribute);
    }

    /**
     * @param PageInterface|Page|DataObject $item
     * @inheritDoc
     */
    protected function canItemBeIndexed(DataObject $item): bool
    {
        if (!$item->isActive()) {
            return false;
        }

        return true;
    }

    /**
     * @param PageInterface|Page|DataObject $entityItem
     * @inheritDoc
     */
    protected function getEntityId(DataObject $entityItem): ?int
    {
        return (int)$entityItem->getId();
    }

    /**
     * @return array
     */
    protected function getIndexedAttributes(): array
    {
        return [
            'title',
            'content_heading',
            'content'
        ];
    }
}
