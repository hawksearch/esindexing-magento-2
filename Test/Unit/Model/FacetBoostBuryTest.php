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
use HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface;
use HawkSearch\EsIndexing\Model\FacetBoostBury;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FacetBoostBuryTest extends TestCase
{
    private FacetBoostBury $model;
    private FacetValueOrderInfoInterface|MockObject $facetValueOrderInfoMock;

    protected function setUp(): void
    {
        $this->facetValueOrderInfoMock = $this->getMockForAbstractClass(FacetValueOrderInfoInterface::class);
        parent::setUp();
    }

    private function initModelEmpty(): void
    {
        $this->model = new FacetBoostBury();
    }

    private function initModelWithValidObjects(): void
    {
        $this->model = new FacetBoostBury(
            [
                FacetBoostBuryInterface::BURY_VALUES => [
                    $this->facetValueOrderInfoMock,
                    $this->facetValueOrderInfoMock
                ],
                FacetBoostBuryInterface::BOOST_VALUES => [$this->facetValueOrderInfoMock],
            ]
        );
    }

    public function testGetBuryValuesEmpty(): void
    {
        $this->initModelEmpty();
        $this->assertEmpty($this->model->getBuryValues());
    }

    public function testGetBoostValuesEmpty(): void
    {
        $this->initModelEmpty();
        $this->assertEmpty($this->model->getBoostValues());
    }

    /**
     * @dataProvider provideTestCasesForGettersWithValidArrayOfObjects
     */
    public function testGettersHasValidArrayOfObjects(
        string $methodName,
        int $valuesCount,
        string $instanceClass
    ): void {
        $this->initModelWithValidObjects();

        $values = $this->model->$methodName();
        $this->assertCount($valuesCount, $values);
        $this->assertContainsOnlyInstancesOf($instanceClass, $values);
    }

    public function provideTestCasesForGettersWithValidArrayOfObjects(): array
    {
        return [
            FacetBoostBuryInterface::BOOST_VALUES => [
                //methodName
                'getBoostValues',
                //valuesCount
                1,
                //instanceClass
                FacetValueOrderInfoInterface::class
            ],
            FacetBoostBuryInterface::BURY_VALUES => [
                'getBuryValues',
                2,
                FacetValueOrderInfoInterface::class
            ]
        ];
    }

    /**
     * @dataProvider provideExceptionTestCasesForConstructor
     */
    public function testConstructorThrowsException(array $data, string $exceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        new FacetBoostBury($data);
    }

    public function provideExceptionTestCasesForConstructor(): array
    {
        return [
            FacetBoostBuryInterface::BOOST_VALUES => [
                //data
                [
                    FacetBoostBuryInterface::BOOST_VALUES => ['non_object'],
                ],
                //exceptionMessage
                'Array keys 0 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface'
            ],
            FacetBoostBuryInterface::BURY_VALUES => [
                [
                    FacetBoostBuryInterface::BURY_VALUES => ['non_object', 'error'],
                ],
                'Array keys 0,1 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface'
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
        $this->initModelEmpty();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->model->$methodName($value);
    }

    public function provideExceptionTestCasesForSettersWithArrayOfObjects(): array
    {
        return [
            FacetBoostBuryInterface::BOOST_VALUES => [
                //methodName
                'setBoostValues',
                //value
                ['non_object'],
                //exceptionMessage
                'Array keys 0 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface'
            ],
            FacetBoostBuryInterface::BURY_VALUES => [
                'setBuryValues',
                ['non_object', 'error'],
                'Array keys 0,1 are not an instance of HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface'
            ]
        ];
    }
}
