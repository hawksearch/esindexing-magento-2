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

use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Helper\PricingHelper;
use HawkSearch\EsIndexing\Model\Config\Products\PriceConfig;
use HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultTypeTest extends TestCase
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

    public function testAccessingDeprecatedMethods(): void
    {
        $this->setUpLegacy($this);

        $model = new TestFixtureSubDefaultTypeLegacy(
            $this->priceCurrencyMock,
            $this->customerGroupSourceMock,
            $this->groupManagementMock,
            $this->moduleManagerMock,
            $this->pricingHelperMock,
            $this->priceConfigMock
        );

        $model->callDeprecatedProtectedMethods($this);

        $this::assertSame(
            [
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addPricesIncludingTax() has been deprecated and it's public/protected usage will be discontinued. Method will be removed. Handle taxes in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addPricesIncludingTax() has been deprecated and it's public/protected usage will be discontinued. Method will be removed. Handle taxes in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addFormattedPrices() has been deprecated and it's public/protected usage will be discontinued. We do not send formatted prices to the index anymore. Handle price formatting in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addFormattedPrices() has been deprecated and it's public/protected usage will be discontinued. We do not send formatted prices to the index anymore. Handle price formatting in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::handleTax() has been deprecated and it's public/protected usage will be discontinued. Method will be removed. Handle taxes in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::handleTax() has been deprecated and it's public/protected usage will be discontinued. Method will be removed. Handle taxes in UI.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addSuffixedValue() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::addSuffixedValue() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::getTierPrices() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::getTierPrices() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::getAllCustomerGroupsId() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
                "Since 0.8.0: Method HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType::getAllCustomerGroupsId() has been deprecated and it's public/protected usage will be discontinued. Method will be removed.",
            ],
            $this->deprecations
        );
    }
}

class TestFixtureSubDefaultTypeLegacy extends DefaultType
{
    public function callDeprecatedProtectedMethods(DefaultTypeTest $test): void
    {
        $this->callAddPricesIncludingTax($test);
        $this->callAddFormattedPrices($test);
        $this->callHandleTax($test);
        $this->callAddSuffixedValue($test);
        $this->callGetTierPrices($test);
        $this->callGetAllCustomerGroupsId($test);
    }

    private function callAddPricesIncludingTax(DefaultTypeTest $test): void
    {
        $productMock = $this->createProductMock($test);
        $priceData = [];
        $this->callMethod('addPricesIncludingTax', $productMock, $priceData);
    }

    private function callAddFormattedPrices(DefaultTypeTest $test): void
    {
        $productMock = $this->createProductMock($test);
        $priceData = [];
        $this->callMethod('addFormattedPrices', $productMock, $priceData);
    }

    private function callHandleTax(DefaultTypeTest $test): void
    {
        $productMock = $this->createProductMock($test);
        $price = 0;
        $this->callMethod('handleTax', $productMock, $price);
    }

    private function callAddSuffixedValue(DefaultTypeTest $test): void
    {
        $priceName = 'test_price';
        $suffix = 'test_suffix';
        $price = 100.0;
        $priceData = [];
        $this->callMethod('addSuffixedValue', $priceName, $suffix, $price, $priceData);
    }

    private function callGetTierPrices(DefaultTypeTest $test): void
    {
        $productMock = $this->createProductMock($test);
        $this->callMethod('getTierPrices', $productMock);
    }

    private function callGetAllCustomerGroupsId(DefaultTypeTest $test): void
    {
        $this->callMethod('getAllCustomerGroupsId');
    }

    private function createProductMock(DefaultTypeTest $test): ProductInterface
    {
        return $test->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    private function callMethod(string $method, mixed ...$params): void
    {
        $ret = $this->$method(...$params);
        $ret = parent::$method(...$params);
    }
}
