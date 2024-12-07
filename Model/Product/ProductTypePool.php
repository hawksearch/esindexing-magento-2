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

namespace HawkSearch\EsIndexing\Model\Product;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

/**
 * @api
 * @since 0.8.0
 */
class ProductTypePool implements ProductTypePoolInterface
{
    /**
     * @var ProductTypeInterface[] | TMap
     */
    private $types;

    /**
     * @var ProductTypeInterfaceFactory
     */
    private $productTypeFactory;

    /**
     * @param TMapFactory $tmapFactory
     * @param ProductTypeInterfaceFactory $productTypeFactory
     * @param array $types
     */
    public function __construct(
        TMapFactory $tmapFactory,
        ProductTypeInterfaceFactory $productTypeFactory,
        array $types = []
    ) {
        $this->productTypeFactory = $productTypeFactory;
        $this->types = $tmapFactory->create(
            [
                'array' => $types,
                'type' => ProductTypeInterface::class
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function get(string $typeCode): ProductTypeInterface
    {
        return isset($this->types[$typeCode]) ? $this->types[$typeCode] : $this->productTypeFactory->create();
    }
}
