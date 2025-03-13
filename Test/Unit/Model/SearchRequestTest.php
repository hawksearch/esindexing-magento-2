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

namespace HawkSearch\EsIndexing\Test\Unit\Model;

use HawkSearch\EsIndexing\Api\Data\BoostQueryInterface;
use HawkSearch\EsIndexing\Api\Data\ClientDataInterface;
use HawkSearch\EsIndexing\Api\Data\ClientDataInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\SmartBarInterface;
use HawkSearch\EsIndexing\Api\Data\SmartBarInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\VariantOptionsInterface;
use HawkSearch\EsIndexing\Api\Data\VariantOptionsInterfaceFactory;
use HawkSearch\EsIndexing\Model\SearchRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class SearchRequestTest extends TestCase
{
    private ClientDataInterface|MockObject $clientDataMock;
    private VariantOptionsInterface|MockObject $variantOptionsMock;
    private SmartBarInterface|MockObject $smartBarMock;
    private BoostQueryInterface|MockObject $boostQueryMock;
    private ClientDataInterfaceFactory|MockObject $clientDataFactoryMock;
    private VariantOptionsInterfaceFactory|MockObject $variantOptionsFactoryMock;
    private SmartBarInterfaceFactory|MockObject $smartBarFactoryMock;
    private SearchRequest $model;

    protected function setUp(): void
    {
        $this->clientDataMock = $this->getMockForAbstractClass(ClientDataInterface::class);
        $this->variantOptionsMock = $this->getMockForAbstractClass(VariantOptionsInterface::class);
        $this->smartBarMock = $this->getMockForAbstractClass(SmartBarInterface::class);
        $this->boostQueryMock = $this->getMockForAbstractClass(BoostQueryInterface::class);

        $this->clientDataFactoryMock = $this->createMock(ClientDataInterfaceFactory::class);
        $this->variantOptionsFactoryMock = $this->createMock(VariantOptionsInterfaceFactory::class);
        $this->smartBarFactoryMock = $this->createMock(SmartBarInterfaceFactory::class);

        $this->clientDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->clientDataMock);
        $this->variantOptionsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->variantOptionsMock);
        $this->smartBarFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->smartBarMock);


        parent::setUp();
    }

    private function initModelWithEmptyData(): void
    {
        $this->model = new SearchRequest(
            $this->clientDataFactoryMock,
            $this->variantOptionsFactoryMock,
            $this->smartBarFactoryMock
        );
    }

    private function initModelWithValidObjects(): void
    {
        $this->model = new SearchRequest(
            $this->clientDataFactoryMock,
            $this->variantOptionsFactoryMock,
            $this->smartBarFactoryMock,
            [
                SearchRequest::FIELD_CLIENT_DATA => $this->clientDataMock,
                SearchRequest::FIELD_VARIANT => $this->variantOptionsMock,
                SearchRequest::FIELD_SMART_BAR => $this->smartBarMock,
                SearchRequest::FIELD_BOOST_QUERIES => [$this->boostQueryMock]
            ]
        );
    }

    public function testGetClientDataDefault(): void
    {
        $this->initModelWithEmptyData();
        $this->assertInstanceOf(ClientDataInterface::class, $this->model->getClientData());
    }

    public function testGetVariantDefault(): void
    {
        $this->initModelWithEmptyData();
        $this->assertInstanceOf(VariantOptionsInterface::class, $this->model->getVariant());
    }

    public function testGetSmartBarDefault(): void
    {
        $this->initModelWithEmptyData();
        $this->assertInstanceOf(SmartBarInterface::class, $this->model->getSmartBar());
    }

    /**
     * @dataProvider provideValidObjectsTestCases
     */
    public function testGettersHasValidArrayOfObjectsAndObjects(
        string $methodName,
        ?int $valuesCount,
        string $instanceClass
    ): void {
        $this->initModelWithValidObjects();

        $values = $this->model->$methodName();

        if (!is_null($valuesCount)) {
            //test array of objects
            $this->assertCount($valuesCount, $values);
            $this->assertContainsOnlyInstancesOf($instanceClass, $values);
        } else {
            //test objects
            $this->assertInstanceOf($instanceClass, $values);
        }
    }

    public function provideValidObjectsTestCases(): array
    {
        return [
            SearchRequest::FIELD_CLIENT_DATA => [
                //methodName
                'getClientData',
                //valuesCount
                null,
                //instanceClass
                ClientDataInterface::class
            ],
            SearchRequest::FIELD_VARIANT => [
                //methodName
                'getVariant',
                //valuesCount
                null,
                //instanceClass
                VariantOptionsInterface::class
            ],
            SearchRequest::FIELD_SMART_BAR => [
                //methodName
                'getSmartBar',
                //valuesCount
                null,
                //instanceClass
                SmartBarInterface::class
            ],
            SearchRequest::FIELD_BOOST_QUERIES => [
                //methodName
                'getBoostQueries',
                //valuesCount
                1,
                //instanceClass
                BoostQueryInterface::class
            ],
        ];
    }

    /**
     * @dataProvider provideExceptionTestCasesForConstructor
     */
    public function testConstructorThrowsException(array $data, string $exceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        new SearchRequest(
            $this->clientDataFactoryMock,
            $this->variantOptionsFactoryMock,
            $this->smartBarFactoryMock,
            $data
        );
    }

    public function provideExceptionTestCasesForConstructor(): array
    {
        return [
            SearchRequest::FIELD_BOOST_QUERIES => [
                //data
                [
                    SearchRequest::FIELD_BOOST_QUERIES => ['non_object', 'error'],
                ],
                //exceptionMessage
                'Array keys 0,1 are not an instance of HawkSearch\EsIndexing\Api\Data\BoostQueryInterface'
            ]
        ];
    }

    /**
     * @dataProvider provideExceptionTestCasesForSettersWithArrayOfObjects
     */
    public function testSettersThrowException(
        string $methodName,
        array $value,
        string $exceptionMessage
    ): void {
        $this->initModelWithEmptyData();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->model->$methodName($value);
    }

    public function provideExceptionTestCasesForSettersWithArrayOfObjects(): array
    {
        return [
            SearchRequest::FIELD_BOOST_QUERIES => [
                //methodName
                'setBoostQueries',
                //value
                ['non_object'],
                //exceptionMessage
                'Array keys 0 are not an instance of HawkSearch\EsIndexing\Api\Data\BoostQueryInterface'
            ],
        ];
    }
}
