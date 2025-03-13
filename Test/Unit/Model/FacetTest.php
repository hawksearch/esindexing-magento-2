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

use HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterface;
use HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface;
use HawkSearch\EsIndexing\Model\Facet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FacetTest extends TestCase
{
    private Facet $model;
    private FacetRangeModelInterface|MockObject $facetRangeModelMock;
    private MockObject|FacetBoostBuryInterface $facetBoostBuryMock;
    private FacetBoostBuryInterfaceFactory|MockObject $facetBoostBuryFactoryMock;

    protected function setUp(): void
    {
        $this->facetRangeModelMock = $this->getMockForAbstractClass(FacetRangeModelInterface::class);
        $this->facetBoostBuryMock = $this->getMockForAbstractClass(FacetBoostBuryInterface::class);
        $this->facetBoostBuryFactoryMock = $this->createMock(FacetBoostBuryInterfaceFactory::class);

        $this->facetBoostBuryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->facetBoostBuryMock);

        parent::setUp();
    }

    private function initModelWithEmptyData(): void
    {
        $this->model = new Facet($this->facetBoostBuryFactoryMock);
    }

    private function initModelWithValidObjects(): void
    {
        $this->model = new Facet(
            $this->facetBoostBuryFactoryMock,
            [
                FacetInterface::FACET_RANGES => [$this->facetRangeModelMock],
                FacetInterface::BOOST_BURY => $this->facetBoostBuryMock,
            ]
        );
    }

    public function testGetBoostBuryDefault(): void
    {
        $this->initModelWithEmptyData();
        $this->assertInstanceOf(FacetBoostBuryInterface::class, $this->model->getBoostBury());
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
            FacetInterface::FACET_RANGES => [
                //methodName
                'getFacetRanges',
                //valuesCount
                1,
                //instanceClass
                FacetRangeModelInterface::class
            ],
            FacetInterface::BOOST_BURY => [
                //methodName
                'getBoostBury',
                //valuesCount
                null,
                //instanceClass
                FacetBoostBuryInterface::class
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
        new Facet($this->facetBoostBuryFactoryMock, $data);
    }

    public function provideExceptionTestCasesForConstructor(): array
    {
        return [
            FacetInterface::FACET_RANGES => [
                //data
                [
                    FacetInterface::FACET_RANGES => ['non_object', 'error'],
                ],
                //exceptionMessage
                'Array keys 0,1 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface'
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
            FacetInterface::FACET_RANGES => [
                //methodName
                'setFacetRanges',
                //value
                ['non_object'],
                //exceptionMessage
                'Array keys 0 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface'
            ],
        ];
    }
}
