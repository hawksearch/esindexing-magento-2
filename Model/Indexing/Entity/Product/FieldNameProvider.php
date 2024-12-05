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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\Product;

use HawkSearch\EsIndexing\Model\Indexing\Field\NameProviderInterface;
use HawkSearch\EsIndexing\Model\Product\Attributes as ProductAttributesProvider;

class FieldNameProvider implements NameProviderInterface
{
    /**
     * @var ProductAttributesProvider
     */
    private ProductAttributesProvider $productAttributes;

    public function __construct(ProductAttributesProvider $productAttributes)
    {
        $this->productAttributes = $productAttributes;
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        return array_fill_keys(array_keys($this->productAttributes->getFieldToAttributeMap()), []);
    }
}
