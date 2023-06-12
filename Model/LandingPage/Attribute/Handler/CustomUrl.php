<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler;

use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;

class CustomUrl implements AttributeHandlerInterface
{
    /**
     * @inheritDoc
     * @todo implement handler which can get other attribute values or has access to the final entity
     * @param CategoryInterface $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        return sprintf("%s", $this->getUrl($item));
    }

    /**
     * @param Category|CategoryInterface $category
     * @return string|null
     */
    protected function getUrl(Category $category)
    {
        return str_replace($category->getUrlInstance()->getBaseUrl(), '', $category->getUrl());
    }
}
