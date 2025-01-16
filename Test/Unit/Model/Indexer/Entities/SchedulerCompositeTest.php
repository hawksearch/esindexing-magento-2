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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities;

use HawkSearch\Connector\Test\Unit\Compatibility\Fixtures\AccessClassPropertyFixtureTrait;
use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchedulerCompositeTest extends TestCase
{
    use LegacyBaseTrait;

    private TMapFactory|MockObject $tmapFactoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $this->tmapFactoryMock = $this->getMockBuilder(TMapFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tmapFactoryMock->expects($this->any())->method('create')
            ->willReturn($this->getMockBuilder(TMap::class)
                ->disableOriginalConstructor()
                ->getMock()
            );
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
    public function testAccessingDeprecatedPropertiesPhp81(string $property, mixed $newPropertyValue, array $deprecationsTriggered): void
    {
        $model = new TestFixtureSubSchedulerCompositeLegacy(
            $this->tmapFactoryMock,
            []
        );

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp81(): array
    {
        return [
            'schedulers' => [
                'schedulers',
                $this->getMockBuilder(TMap::class)
                    ->disableOriginalConstructor()
                    ->addMethods(['fakeMethod'])
                    ->getMock(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
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
    public function testAccessingDeprecatedPropertiesPhp82(string $property, mixed $newPropertyValue, array $deprecationsTriggered): void
    {
        $model = new TestFixtureSubSchedulerCompositeLegacy(
            $this->tmapFactoryMock,
            []
        );

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp82(): array
    {
        return [
            'schedulers' => [
                'schedulers',
                $this->getMockBuilder(TMap::class)
                    ->disableOriginalConstructor()
                    ->addMethods(['fakeMethod'])
                    ->getMock(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities\TestFixtureSubSchedulerCompositeLegacy::\$schedulers is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerComposite::schedulers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
        ];
    }
}

class TestFixtureSubSchedulerCompositeLegacy extends SchedulerComposite
{
    use AccessClassPropertyFixtureTrait;
}
