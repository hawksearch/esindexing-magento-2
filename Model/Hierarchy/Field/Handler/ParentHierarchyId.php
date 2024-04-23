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

namespace HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler;

use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\DataObject;

class ParentHierarchyId implements FieldHandlerInterface
{

    /**
     * @inheritDoc
     * @param CategoryInterface $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        if ($item->getLevel() == 1) {
            return 0;
        } else {
            return (int)$item->getParentId();
        }
    }
}
