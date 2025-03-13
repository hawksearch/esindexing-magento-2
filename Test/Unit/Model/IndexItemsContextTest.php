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

use HawkSearch\EsIndexing\Api\Data\IndexItemInterface;
use HawkSearch\EsIndexing\Model\IndexItemsContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexItemsContextTest extends TestCase
{
    private IndexItemInterface|MockObject $itemsMock;
    private IndexItemsContext $model;

    protected function setUp(): void
    {
        $this->itemsMock = $this->getMockForAbstractClass(IndexItemInterface::class);

        parent::setUp();
    }

    private function initModelWithEmptyData(): void
    {
        $this->model = new IndexItemsContext();
    }

    private function initModelWithValidObjects(): void
    {
        $this->model = new IndexItemsContext(
            [
                IndexItemsContext::FIELD_ITEMS => [$this->itemsMock, $this->itemsMock],
            ]
        );
    }

    /**
     * @dataProvider provideValidObjectsTestCases
     */
    public function testGettersHasValidArrayOfObjects(
        string $methodName,
        ?int $valuesCount,
        string $instanceClass
    ): void {
        $this->initModelWithValidObjects();

        $values = $this->model->$methodName();

        $this->assertCount($valuesCount, $values);
        $this->assertContainsOnlyInstancesOf($instanceClass, $values);
    }

    public function provideValidObjectsTestCases(): array
    {
        return [
            IndexItemsContext::FIELD_ITEMS => [
                //methodName
                'getItems',
                //valuesCount
                2,
                //instanceClass
                IndexItemInterface::class
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
        new IndexItemsContext($data);
    }

    public function provideExceptionTestCasesForConstructor(): array
    {
        return [
            IndexItemsContext::FIELD_ITEMS => [
                //data
                [
                    IndexItemsContext::FIELD_ITEMS => ['non_object', 'error'],
                ],
                //exceptionMessage
                'Array keys 0,1 are not an instance of HawkSearch\EsIndexing\Api\Data\IndexItemInterface'
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
            IndexItemsContext::FIELD_ITEMS => [
                //methodName
                'setItems',
                //value
                ['non_object'],
                //exceptionMessage
                'Array keys 0 are not an instance of HawkSearch\EsIndexing\Api\Data\IndexItemInterface'
            ],
        ];
    }
}
