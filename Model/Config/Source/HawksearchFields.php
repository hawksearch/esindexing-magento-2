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

namespace HawkSearch\EsIndexing\Model\Config\Source;

use HawkSearch\Connector\Api\Data\HawkSearchFieldInterface;
use HawkSearch\EsIndexing\Api\FieldsManagementInterface;
use Magento\Framework\Data\OptionSourceInterface;

class HawksearchFields implements OptionSourceInterface
{
    /**
     * @var FieldsManagementInterface
     */
    private $fieldsManagement;

    /**
     * @var array
     */
    private $fieldsCache;

    /**
     * @param FieldsManagementInterface $fieldsManagement
     */
    public function __construct(
        FieldsManagementInterface $fieldsManagement

    ) {
        $this->fieldsManagement = $fieldsManagement;
    }

    /**
     * @inheritdoc
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
     * @return HawkSearchFieldInterface[]
     */
    private function getFields()
    {
        if ($this->fieldsCache === null) {
            $this->fieldsCache = $this->fieldsManagement->getHawkSearchFields();
        }

        return $this->fieldsCache;
    }
}
