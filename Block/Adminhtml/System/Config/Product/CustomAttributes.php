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

namespace HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product;

use HawkSearch\Connector\Compatibility\PublicPropertyDeprecationTrait;
use HawkSearch\EsIndexing\Block\Adminhtml\Form\Field\Select;
use HawkSearch\EsIndexing\Model\Config\Backend\Serialized\Processor\ValueProcessorInterface;
use HawkSearch\EsIndexing\Model\Config\Source\HawksearchFields;
use HawkSearch\EsIndexing\Model\Config\Source\ProductAttributes;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CustomAttributes extends AbstractFieldArray
{
    use PublicPropertyDeprecationTrait;

    private array $deprecatedPublicProperties = [
        'columnRendererCache' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private.'
        ]
    ];

    /**
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     * @var array<string, mixed>
     */
    private array $columnRendererCache = [];
    private HawksearchFields $hawksearchFields;
    private ProductAttributes $productAttributes;

    /**
     * @param Template\Context $context
     * @param HawksearchFields $hawksearchFields
     * @param ProductAttributes $productAttributes
     * @param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        HawksearchFields $hawksearchFields,
        ProductAttributes $productAttributes,
        array $data = []
    )
    {
        $this->hawksearchFields = $hawksearchFields;
        $this->productAttributes = $productAttributes;
        parent::__construct($context, $data);
    }

    /**
     * Define columns
     */
    protected function _construct(): void
    {
        $this->setHtmlId('_' . uniqid());
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New Mapping');

        $this->addColumn(
            ValueProcessorInterface::COLUMN_FIELD,
            [
                'label' => __('Hawk Field Name'),
                'class' => 'required-entry',
                'options' => function () {
                    return $this->hawksearchFields->toOptionArray();
                },
            ]
        );

        $this->addColumn(
            ValueProcessorInterface::COLUMN_ATTRIBUTE,
            [
                'label' => __('Product Attribute'),
                'options' => function () {
                    return $this->productAttributes->toOptionArray();
                },
            ]
        );

        parent::_construct();
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array<string, mixed> $params
     * @return void
     */
    public function addColumn($name, $params)
    {
        parent::addColumn($name, $params);
        if (!isset($this->_columns[$name])) {
            return;
        }

        $this->_columns[$name]['readonly'] = $this->_getParam($params, 'readonly', false);

        $options = $this->_getParam($params, 'options');
        if ($options !== null) {
            $this->_columns[$name]['options'] = $options;
        }
        $this->_columns[$name]['renderer'] = $this->getColumnRenderer($name);
    }

    /**
     * Set select options attributes
     *
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        foreach ($this->_columns as $columnName => $columnData) {
            if (isset($columnData['options'])) {
                $index = 'option_' . $this->getColumnRenderer($columnName)
                        ->calcOptionHash($row->getData($columnName));

                $options[$index] = 'selected="selected"';
            }
        }

        if ($row['_id'] === null || is_int($row['_id'])) {
            $row->setData('_id', '_' . rand(1000000000, 9999999999) . '_' . rand(0, 999));
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function getColumnRenderer(string $columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }

        $columnData = $this->_columns[$columnName];
        if (!array_key_exists($columnName, $this->columnRendererCache) || !$this->columnRendererCache[$columnName]) {
            $renderer = $this->resolveSelectFieldRenderer($columnName);

            $renderer = $columnData['renderer'] ?: $renderer ?? false;

            $this->columnRendererCache[$columnName] = $renderer;
        }

        return $this->columnRendererCache[$columnName];
    }

    /**
     * @return Select|null
     * @throws \Exception
     */
    protected function resolveSelectFieldRenderer(string $columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }

        $columnData = $this->_columns[$columnName];
        if (empty($columnData['options'])) {
            return null;
        }

        /** @var Select $renderer */
        $renderer = $this->getLayout()->createBlock(
            Select::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );

        $options = $columnData['options'];
        if (is_callable($options)) {
            $options = $options();
        }

        $renderer->setOptions($options);

        return $renderer;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        $block = $this->getLayout()->createBlock(
            Template::class
        )->setTemplate(
            'HawkSearch_EsIndexing::system/config/product/custom-attributes-js.phtml'
        )->setData(
            [
                'html_id' => $this->getHtmlId(),
                'new_field_option_value' => ValueProcessorInterface::SELECT_OPTION_NEW_FILED_VALUE,
                'base_class_prefix' => 'arrayRow'
            ]
        );

        return $html . $block->toHtml();
    }
}
