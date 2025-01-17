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

namespace HawkSearch\EsIndexing\Model\Product\ProductType;

use HawkSearch\Connector\Compatibility\PublicPropertyDeprecationTrait;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Api\Data\ProductInterface;

class Bundle extends CompositeType
{
    use PublicPropertyDeprecationTrait;

    private array $deprecatedPublicProperties = [
        'keySelectionsCollection' => [
            'since' => '0.7.0',
            'description' => 'Property will be removed.'
        ],
    ];

    /**
     * @private since 0.7.0 will be removed
     */
    private string $keySelectionsCollection = '_cache_instance_selections_collection_hawksearch';

    /**
     * @inheritDoc
     */
    protected function getMinMaxPrice(ProductInterface $product): array
    {
        /** @var Price $priceModel */
        $priceModel = $product->getPriceModel();
        [$min, $max] = $priceModel->getTotalPrices($product, null, true, true);

        return [(float)$min, (float)$max];
    }

}
