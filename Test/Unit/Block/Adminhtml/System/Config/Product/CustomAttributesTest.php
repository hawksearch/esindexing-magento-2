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
use HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes;
use HawkSearch\EsIndexing\Model\Config\Source\HawksearchFields;
use HawkSearch\EsIndexing\Model\Config\Source\ProductAttributes;
use Magento\Backend\Block\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomAttributesTest extends TestCase
{
    use LegacyBaseTrait;

    private Context|MockObject $contextMock;
    private MockObject|HawksearchFields $hawksearchFieldsMock;
    private MockObject|ProductAttributes $productAttributesMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->hawksearchFieldsMock = $this->getMockBuilder(HawksearchFields::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productAttributesMock = $this->getMockBuilder(ProductAttributes::class)
            ->disableOriginalConstructor()
            ->getMock();
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
}

/**
 * @group legacy
 */
class TestFixtureSubCustomAttributesLegacy extends CustomAttributes
{
    use AccessClassPropertyFixtureTrait;
}
