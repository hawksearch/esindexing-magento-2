<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Test\Unit\Block\Adminhtml\System\Config\Product;

use HawkSearch\Connector\Test\Unit\Compatibility\Fixtures\AccessClassPropertyFixtureTrait;
use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Block\Adminhtml\Form\Field\Select as FormFieldSelect;
use HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes;
use HawkSearch\EsIndexing\Model\Config\Backend\Serialized\Processor\ValueProcessorInterface;
use HawkSearch\EsIndexing\Model\Config\Source\HawksearchFields;
use HawkSearch\EsIndexing\Model\Config\Source\ProductAttributes;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Layout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomAttributesTest extends TestCase
{
    use LegacyBaseTrait;

    private const OPTIONS_FIELD = [
        [
            'value' => '',
            'label' => '--Please Select Field--'
        ],
        [
            'value' => 'test_field_id',
            'label' => 'Test Field'
        ],
    ];
    private const OPTIONS_ATTRIBUTE = [
        [
            'value' => '',
            'label' => '--Please Select Attribute--'
        ],
        [
            'value' => 'test_attribute_id',
            'label' => 'Test Attribute'
        ],
    ];

    private Context|MockObject $contextMock;
    private MockObject|HawksearchFields $hawksearchFieldsMock;
    private MockObject|ProductAttributes $productAttributesMock;
    /**
     * @var array<string, mixed>
     */
    private array $elementTestData;
    /**
     * @var array<string, mixed>
     */
    private array $blockTestData;
    private Text|MockObject $elementMock;
    private MockObject|Layout $layoutMock;
    private CustomAttributes $block;
    private Template|MockObject $templateBlockMock;
    private MockObject|FormFieldSelect $selectBlockMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $eventManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->layoutMock = $this->createMock(Layout::class);
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getEventManager')
            ->willReturn($eventManagerMock);
        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $this->hawksearchFieldsMock = $this->getMockBuilder(HawksearchFields::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productAttributesMock = $this->getMockBuilder(ProductAttributes::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementMock = $this->getMockBuilder(Text::class)
            ->addMethods([
                'getLabel',
                'getValue',
            ])
            ->onlyMethods(['getHtmlId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementTestData = [
            'htmlId' => 'test_element_field_id',
            'label' => 'test_element_label',
        ];
        $this->elementMock->expects(
            $this->any()
        )->method(
            'getHtmlId'
        )->willReturn(
            $this->elementTestData['htmlId']
        );
        $this->elementMock->expects(
            $this->any()
        )->method(
            'getLabel'
        )->willReturn(
            $this->elementTestData['label']
        );

        $this->templateBlockMock = $this->getMockBuilder(Template::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setTemplate', 'setData', 'toHtml'])
            ->getMock();

        $this->selectBlockMock = $this->getMockBuilder(FormFieldSelect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutMock->expects($this->any())
            ->method('createBlock')
            ->willReturnMap([
                [Template::class, '', [], $this->templateBlockMock],
                [
                    FormFieldSelect::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]],
                    $this->selectBlockMock
                ],
            ]);

        $this->blockTestData = [
            'template' => '',
            'name' => 'CustomAttributesTest_name',
            'id' => 'CustomAttributesTest_id',
        ];
        $this->block = new CustomAttributes(
            $this->contextMock,
            $this->hawksearchFieldsMock,
            $this->productAttributesMock,
            $this->blockTestData
        );
        $this->block->setLayout($this->layoutMock);
    }

    protected function tearDown(): void
    {
        $this->tearDownLegacy($this);
        parent::tearDown();
    }

    /**
     * @requires PHP <8.2.0
     * @group legacy
     */
    #[RequiresPhp('<8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp81(): void
    {
        $model = new TestFixtureSubCustomAttributesLegacy(
            $this->contextMock,
            $this->hawksearchFieldsMock,
            $this->productAttributesMock,
            []
        );

        $this->assertLegacyProperty('columnRendererCache', ['test_renderer' => []], $model, $this, [
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
        ]);
    }

    /**
     * @requires PHP >=8.2.0
     * @group legacy
     */
    #[RequiresPhp('>=8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp82(): void
    {
        $model = new TestFixtureSubCustomAttributesLegacy(
            $this->contextMock,
            $this->hawksearchFieldsMock,
            $this->productAttributesMock,
            []
        );

        $this->assertLegacyProperty('columnRendererCache', ['test_renderer' => []], $model, $this, [
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Block\Adminhtml\System\Config\Product\TestFixtureSubCustomAttributesLegacy::\$columnRendererCache is deprecated",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private.",
        ]);
    }

    public function testRender(): void
    {
        $templateBlockTestData = [
            'html' => 'template_block_test',
        ];

        $this->templateBlockMock->expects($this->once())
            ->method('setTemplate')
            ->with('HawkSearch_EsIndexing::system/config/product/custom-attributes-js.phtml')
            ->willReturnSelf();
        $this->templateBlockMock->expects($this->once())
            ->method('setData')
            ->with([
                'html_id' => $this->block->getHtmlId(),
                'new_field_option_value' => ValueProcessorInterface::SELECT_OPTION_NEW_FILED_VALUE,
                'base_class_prefix' => 'arrayRow'
            ])
            ->willReturnSelf();
        $this->templateBlockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($templateBlockTestData['html']);


        $expectedHtml = '<tr id="row_test_element_field_id"><td class="label"><label for="test_element_field_id"><span>test_element_label</span></label></td><td class="value">template_block_test</td><td class=""></td></tr>';
        $this->assertEquals($expectedHtml, $this->block->render($this->elementMock));
    }

    private function initBlockMocksForColumnTests(): void
    {
        $this->templateBlockMock->expects($this->once())
            ->method('setTemplate')
            ->willReturnSelf();
        $this->templateBlockMock->expects($this->once())
            ->method('setData')
            ->willReturnSelf();
        $this->block->render($this->elementMock);
    }

    public function testColumnsCountAndOrder(): void
    {
        $this->initBlockMocksForColumnTests();

        $columns = $this->block->getColumns();
        $expectedOrder = array_keys($this->provideColumndsData());
        $actualOrder = array_keys($columns);

        $this->assertSameSize(
            $this->provideColumndsData(),
            $this->block->getColumns(),
            'Test columns count'
        );
        $this->assertEquals(
            $expectedOrder,
            $actualOrder,
            'Test columns ordered correctly'
        );
    }

    /**
     * @depends      testColumnsCountAndOrder
     * @dataProvider provideColumndsData
     */
    public function testColumnsDataIsValid(string $columnName, bool $required, string $label, array $options): void
    {
        $this->initBlockMocksForColumnTests();
        $this->hawksearchFieldsMock->expects($this->any())
            ->method('toOptionArray')
            ->willReturn(self::OPTIONS_FIELD);
        $this->productAttributesMock->expects($this->any())
            ->method('toOptionArray')
            ->willReturn(self::OPTIONS_ATTRIBUTE);

        $columns = $this->block->getColumns();

        // test required
        if ($required) {
            $this->assertArrayHasKey('class', $columns[$columnName], 'Test column is required');
            $this->assertNotNull($columns[$columnName]['class'], 'Test column is required');
            $this->assertStringContainsString('required-entry', $columns[$columnName]['class'], 'Test column is required');
        }

        // test label
        $this->assertArrayHasKey('label', $columns[$columnName], 'Test column label');
        $this->assertEquals($label, $columns[$columnName]['label'], 'Test column label');

        // test options
        if (!empty($options)) {
            $this->assertArrayHasKey('options', $columns[$columnName], 'Test column options');
            $this->assertIsCallable($columns[$columnName]['options'], 'Test column options');
            $this->assertEquals($options, $columns[$columnName]['options']());
        }

        // test readonly param added
        $this->assertArrayHasKey('readonly', $columns[$columnName], 'Test `readonly` param added');
    }

    public function provideColumndsData(): array
    {
        return [
            ValueProcessorInterface::COLUMN_FIELD => [
                //  $columnName
                ValueProcessorInterface::COLUMN_FIELD,
                // $required
                true,
                // $label
                'Hawk Field Name',
                // $options
                self::OPTIONS_FIELD,
            ],
            ValueProcessorInterface::COLUMN_ATTRIBUTE => [
                ValueProcessorInterface::COLUMN_ATTRIBUTE,
                false,
                'Product Attribute',
                self::OPTIONS_ATTRIBUTE,
            ]
        ];
    }

    /**
     * @return void
     */
    public function testGetAddButtonLabel(): void
    {
        $this->assertEquals("Add New Mapping", $this->block->getAddButtonLabel());
    }

    public function testIsAddAfter(): void
    {
        $this->assertFalse($this->block->isAddAfter());
    }

    public function testGetArrayRows_option_extra_attrs(): void
    {
        $rowsTestOptions = $rowsTestData = [
            'row_1' => [
                'field' => 'row_1_field',
                'attribute' => 'row_1_attribute',
            ],
            'row_2' => [
                'field' => 'row_2_field',
                'attribute' => 'row_2_attribute',
            ],
        ];
        $map = [];

        $columns = $this->block->getColumns();
        foreach ($rowsTestData as $rowId => $rowData) {
            foreach ($rowData as $field => $value) {
                if (isset($columns[$field]['options'])) {
                    $map[] = [$value, $value];
                    $rowsTestOptions[$rowId][$field] = 'option_' . $value;
                } else {
                    unset($rowsTestOptions[$rowId][$field]);
                }
            }
        }

        $this->elementMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($rowsTestData);
        $this->block->setElement($this->elementMock);

        $this->selectBlockMock->expects($this->any())
            ->method('calcOptionHash')
            ->willReturnMap($map);

        $rows = $this->block->getArrayRows();

        foreach ($rows as $rowKey => $rowData) {
            $this->assertEquals(array_values($rowsTestOptions[$rowKey]), array_keys($rowData['option_extra_attrs']));
        }
    }

    /**
     * @dataProvider provideRowIdTestData
     */
    public function testGetArrayRows_rowId(mixed $rowId, array $rowData, bool $result): void
    {
        $rowsTestData = [
            $rowId => $rowData
        ];

        $this->elementMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($rowsTestData);
        $this->block->setElement($this->elementMock);

        $row = current($this->block->getArrayRows());

        $this->assertEquals($rowId === $row['_id'], $result);
    }

    public function provideRowIdTestData(): array
    {
        return [
            'string row id' => [
                'string_row_id',
                [
                    'field' => 'row_1_field',
                    'attribute' => 'row_1_attribute',
                ],
                true
            ],
            'int row id' => [
                0,
                [
                    'field' => 'row_1_field',
                    'attribute' => 'row_1_attribute',
                ],
                false
            ],
        ];
    }
}

/**
 * @group legacy
 */
class TestFixtureSubCustomAttributesLegacy extends CustomAttributes
{
    use AccessClassPropertyFixtureTrait;
}
