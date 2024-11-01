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

namespace HawkSearch\EsIndexing\Model\Product\Attribute;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * @internal experimental feature
 */
class ValueProcessor implements ValueProcessorInterface
{
    private const SYSTEM_ATTRIBUTES = [
        'type_id',
        'thumbnail_url',
        'image_url',
        'category',
        'url',
        'visibility',
        'status'
    ];

    /**
     * @inheritDoc
     */
    public function process(Attribute $attribute, array $value, array $relatedValues = [])
    {
        if ($this->isRollUpAttributeStrategy($attribute)) {
            $value = array_merge($value, $relatedValues);
        }

        if ($this->isUniqueValueStrategy($attribute)) {
            $value = array_unique($value);
        }

        return $value;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function isRollUpAttributeStrategy(Attribute $attribute)
    {
        $isStatic = $attribute->getBackendType() === 'static';
        $isSystemAttribute = in_array($attribute->getAttributeCode(), self::SYSTEM_ATTRIBUTES);
        return !$isStatic && !$isSystemAttribute;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function isUniqueValueStrategy(Attribute $attribute)
    {
        return true;
    }
}
