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

namespace HawkSearch\EsIndexing\Model\Config\Source;

use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use HawkSearch\EsIndexing\Api\FieldManagementInterface;
use Magento\Framework\Data\OptionSourceInterface;

class HawksearchFields implements OptionSourceInterface
{
    /**
     * @var FieldInterface[]
     */
    private array $fieldsCache;
    private FieldManagementInterface $fieldManagement;

    public function __construct(
        FieldManagementInterface $fieldManagement

    ) {
        $this->fieldManagement = $fieldManagement;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getFields() as $field) {
            $options[] = [
                'value' => $field->getName(),
                'label' => $field->getName()
            ];
        }
        array_unshift($options, [
            'value' => null,
            'label' => '--Please Select--'
        ]);

        return $options;
    }

    /**
     * @return FieldInterface[]
     */
    private function getFields(): array
    {
        if (!isset($this->fieldsCache)) {
            $this->fieldsCache = $this->fieldManagement->getFields();
        }

        return $this->fieldsCache;
    }
}
