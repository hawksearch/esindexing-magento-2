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

namespace HawkSearch\EsIndexing\Model\Product\Price;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class ProductTypePool implements ProductTypePoolInterface
{
    /**
     * @var ProductTypeInterface[] | TMap
     */
    private $types;

    /**
     * @param TMapFactory $tmapFactory
     * @param array $types
     */
    public function __construct(
        TMapFactory $tmapFactory,
        array $types = []
    ) {
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
        if (!isset($this->types[$typeCode])) {
            throw new NotFoundException(
                __(
                    'The "%1" product price type doesn\'t exist. Verify the product price type code and try again.',
                    $typeCode
                )
            );
        }

        return $this->types[$typeCode];
    }
}
