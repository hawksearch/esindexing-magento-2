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

use HawkSearch\EsIndexing\Api\Data\CoordinateInterface;
use HawkSearch\EsIndexing\Api\Data\CoordinateInterfaceFactory;
use HawkSearch\EsIndexing\Model\ClientData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientDataTest extends TestCase
{
    private CoordinateInterface|MockObject $coordinateMock;
    private CoordinateInterfaceFactory|MockObject $coordinateFactoryMock;
    private ClientData $model;

    protected function setUp(): void
    {
        $this->coordinateMock = $this->getMockForAbstractClass(CoordinateInterface::class);
        $this->coordinateFactoryMock = $this->createMock(CoordinateInterfaceFactory::class);

        $this->coordinateFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->coordinateMock);

        parent::setUp();
    }

    private function initModelWithEmptyData(): void
    {
        $this->model = new ClientData($this->coordinateFactoryMock);
    }

    private function initModelWithValidObjects(): void
    {
        $this->model = new ClientData(
            $this->coordinateFactoryMock,
            [
                ClientData::FIELD_ORIGIN => $this->coordinateMock,
            ]
        );
    }

    public function testGetOriginDefault(): void
    {
        $this->initModelWithEmptyData();
        $this->assertInstanceOf(CoordinateInterface::class, $this->model->getOrigin());
    }

    /**
     * @dataProvider provideValidObjectsTestCases
     */
    public function testGettersHasValidObjects(
        string $methodName,
        string $instanceClass
    ): void {
        $this->initModelWithValidObjects();
        $values = $this->model->$methodName();
        $this->assertInstanceOf($instanceClass, $values);
    }

    public function provideValidObjectsTestCases(): array
    {
        return [
            ClientData::FIELD_ORIGIN => [
                //methodName
                'getOrigin',
                //instanceClass
                CoordinateInterface::class
            ]
        ];
    }
}
