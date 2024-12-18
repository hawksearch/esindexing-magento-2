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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Hierarchy;

use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\DataObject;

/**
 * @phpstan-type ItemType DataObject&CategoryInterface
 * @extends AbstractEntityRebuild<ItemType>
 */
class EntityRebuild extends AbstractEntityRebuild
{
    protected function isAllowedItem(DataObject $item): bool
    {
        $category = $item;
        if ($category->getId()) {
            while ($category->getLevel() != 0) {
                if (!$category->getIsActive()) {
                    return false;
                }
                $category = $category->getParentCategory();
            }

            return true;
        }

        return false;
    }

    protected function getEntityId(DataObject $entityItem): ?int
    {
        return (int)$entityItem->getId();
    }
    
    protected function castAttributeValue(mixed $value)
    {
        return $value === '' ? null : $value;
    }
}
