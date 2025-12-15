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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Product\ProductType;

use HawkSearch\Connector\Test\Unit\Compatibility\Fixtures\AccessClassPropertyFixtureTrait;
use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Helper\PricingHelper;
use HawkSearch\EsIndexing\Model\Config\Products\PriceConfig;
use HawkSearch\EsIndexing\Model\Product\ProductType\Bundle;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    use LegacyBaseTrait;

    private PriceCurrencyInterface|MockObject $priceCurrencyMock;
    private GroupSourceInterface|MockObject $customerGroupSourceMock;
    private GroupManagementInterface|MockObject $groupManagementMock;
    private ModuleManager|MockObject $moduleManagerMock;
    private PricingHelper|MockObject $pricingHelperMock;
    private PriceConfig|MockObject $priceConfigMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $this->priceCurrencyMock = $this->getMockBuilder(PriceCurrencyInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->customerGroupSourceMock = $this->getMockBuilder(GroupSourceInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->groupManagementMock = $this->getMockBuilder(GroupManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->moduleManagerMock = $this->getMockBuilder(ModuleManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pricingHelperMock = $this->getMockBuilder(PricingHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceConfigMock = $this->getMockBuilder(PriceConfig::class)
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
     * @dataProvider provideLegacyPropertiesPhp81
     */
    #[RequiresPhp('<8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp81(
        string $property,
        mixed $newPropertyValue,
        array $deprecationsTriggered
    ): void {
        $model = new TestFixtureSubBundleLegacy(
            $this->priceCurrencyMock,
            $this->customerGroupSourceMock,
            $this->groupManagementMock,
            $this->moduleManagerMock,
            $this->pricingHelperMock,
            $this->priceConfigMock
        );

        $newPropertyValue = $newPropertyValue instanceof \Closure ? $newPropertyValue->bindTo($this)() : $newPropertyValue;

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp81(): array
    {
        return [
            'keySelectionsCollection' => [
                'keySelectionsCollection',
                'test string',
                [
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                ]
            ],
        ];
    }

    /**
     * @requires PHP >=8.2.0
     * @group legacy
     * @dataProvider provideLegacyPropertiesPhp82
     */
    #[RequiresPhp('>=8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp82(
        string $property,
        mixed $newPropertyValue,
        array $deprecationsTriggered
    ): void {
        $model = new TestFixtureSubBundleLegacy(
            $this->priceCurrencyMock,
            $this->customerGroupSourceMock,
            $this->groupManagementMock,
            $this->moduleManagerMock,
            $this->pricingHelperMock,
            $this->priceConfigMock
        );

        $newPropertyValue = $newPropertyValue instanceof \Closure ? $newPropertyValue->bindTo($this)() : $newPropertyValue;

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp82(): array
    {
        return [
            'keySelectionsCollection' => [
                'keySelectionsCollection',
                'test string',
                [
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Product\ProductType\TestFixtureSubBundleLegacy::\$keySelectionsCollection is deprecated",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                    "Since 0.7.0: Property HawkSearch\EsIndexing\Model\Product\ProductType\Bundle::keySelectionsCollection has been deprecated and it's public/protected usage will be discontinued. Property will be removed.",
                ]
            ],
        ];
    }
}

class TestFixtureSubBundleLegacy extends Bundle
{
    use AccessClassPropertyFixtureTrait;
}
