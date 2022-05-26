<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;

class Attributes
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * Attributes constructor.
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllAttributes()
    {
        if (!isset($this->attributes)) {
            $productEntityAttributes = array_merge(
                $this->getMandatoryAttributes(),
                $this->eavConfig->getEntityAttributeCodes(Product::ENTITY)
            );

            $productEntityAttributes = array_diff($productEntityAttributes, $this->getExcludedAttributes());

            foreach ($productEntityAttributes as $code) {
                $this->attributes[$code] = $this->eavConfig
                    ->getAttribute(Product::ENTITY, $code)
                    ->getFrontendLabel();
            }

            ksort($this->attributes);
        }

        return $this->attributes;
    }

    /**
     * Returns list of product attributes which should be skipped by indexing processor.
     * Attributes from this list are excluded from any visual interfaces.
     *
     * @return array
     */
    public function getExcludedAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getMandatoryAttributes()
    {
        return [
            //'entity_id',
            'type_id',
            'thumbnail_url',
            'image_url',
            'name',
            'category',
            'url'
        ];
    }

    /**
     * Product data which is calculated
     * @return array
     */
    public function getExtraDataCodes()
    {
        return [];
    }
}
