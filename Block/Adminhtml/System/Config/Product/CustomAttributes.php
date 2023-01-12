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

use HawkSearch\EsIndexing\Block\Adminhtml\Form\Field\Select;
use HawkSearch\EsIndexing\Model\Config\Source\HawksearchFields;
use HawkSearch\EsIndexing\Model\Config\Source\ProductAttributes;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CustomAttributes extends AbstractFieldArray
{
    /**#@+
     * Constants
     */
    const COLUMN_ATTRIBUTE = 'attribute';
    const COLUMN_FIELD = 'field';
    const SELECT_OPTION_NEW_FILED_VALUE = '--insert--new--';
    /**#@-*/

    /**
     * @var array
     */
    protected $columnRendererCache = [];

    /**
     * @var HawksearchFields
     */
    private HawksearchFields $hawksearchFields;

    /**
     * @var ProductAttributes
     */
    private ProductAttributes $productAttributes;

    /**
     * @param Template\Context $context
     * @param HawksearchFields $hawksearchFields
     * @param ProductAttributes $productAttributes
     * @param array $data
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
     * Prepare rendering the new field by adding all the needed columns
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            self::COLUMN_FIELD,
            [
                'label' => __('Hawk Field Name'),
                'class' => 'required-entry',
                'options' => function() {
                    return $this->hawksearchFields->toOptionArray();
                },
            ]
        );

        $this->addColumn(
            self::COLUMN_ATTRIBUTE,
            [
                'label' => __('Product Attribute'),
                'options' => function() {
                    return $this->productAttributes->toOptionArray();
                },
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New Mapping');
        $this->setHtmlId('_' . uniqid());
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
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
     * Prepare existing row data object
     *
     * @param DataObject $row
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

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @param string $columnName
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function getColumnRenderer($columnName)
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
     * @param string $columnName
     * @return Select|null
     * @throws \Exception
     */
    protected function resolveSelectFieldRenderer($columnName)
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

    /**
     * @inheritDoc
     */
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
                'new_field_option_value' => self::SELECT_OPTION_NEW_FILED_VALUE,
                'base_class_prefix' => 'arrayRow'
            ]
        );

        return $html . $block->toHtml();
    }
}
