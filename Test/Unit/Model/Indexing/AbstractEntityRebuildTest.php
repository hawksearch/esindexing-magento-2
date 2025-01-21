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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Indexing;

use HawkSearch\Connector\Logger\LoggerFactoryInterface;
use HawkSearch\Connector\Test\Unit\Compatibility\Fixtures\AccessClassPropertyFixtureTrait;
use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild;
use HawkSearch\EsIndexing\Model\Indexing\ContextInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypePoolInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbstractEntityRebuildTest extends TestCase
{
    use LegacyBaseTrait;

    private MockObject|EntityTypePoolInterface $entityTypePoolMock;
    private EventManagerInterface|MockObject $eventManagerMock;
    private MockObject|LoggerFactoryInterface $loggerFactoryMock;
    private MockObject|StoreManagerInterface $storeManagerMock;
    private MockObject|ContextInterface $indexingContextMock;
    private ObjectHelper|MockObject $objectHelperMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $this->entityTypePoolMock = $this->getMockBuilder(EntityTypePoolInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->eventManagerMock = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->loggerFactoryMock = $this->getMockBuilder(LoggerFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->indexingContextMock = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->objectHelperMock = $this->getMockBuilder(ObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerFactoryMock->expects($this->any())->method('create')
            ->willReturn($this->getMockBuilder(LoggerInterface::class)
                ->disableOriginalConstructor()
                ->getMockForAbstractClass()
            );
        $this->setUpBeforeClass();
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
        $model = new TestFixtureSubAbstractEntityRebuildLegacy(
            $this->entityTypePoolMock,
            $this->eventManagerMock,
            $this->loggerFactoryMock,
            $this->storeManagerMock,
            $this->indexingContextMock,
            $this->objectHelperMock
        );

        $newPropertyValue = $newPropertyValue instanceof \Closure ? $newPropertyValue->bindTo($this)() : $newPropertyValue;

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp81(): array
    {
        return [
            'entityTypePool' => [
                'entityTypePool',
                $this->getMockBuilder(EntityTypePoolInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'eventManager' => [
                'eventManager',
                $this->getMockBuilder(EventManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'hawkLogger' => [
                'hawkLogger',
                function (): object {
                    return clone $this->loggerFactoryMock->create();
                },
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                ]
            ],
            'storeManager' => [
                'storeManager',
                $this->getMockBuilder(StoreManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'indexingContext' => [
                'indexingContext',
                $this->getMockBuilder(ContextInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ]
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
        $model = new TestFixtureSubAbstractEntityRebuildLegacy(
            $this->entityTypePoolMock,
            $this->eventManagerMock,
            $this->loggerFactoryMock,
            $this->storeManagerMock,
            $this->indexingContextMock,
            $this->objectHelperMock
        );

        $newPropertyValue = $newPropertyValue instanceof \Closure ? $newPropertyValue->bindTo($this)() : $newPropertyValue;

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp82(): array
    {
        return [
            'entityTypePool' => [
                'entityTypePool',
                $this->getMockBuilder(EntityTypePoolInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\TestFixtureSubAbstractEntityRebuildLegacy::\$entityTypePool is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::entityTypePool has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'eventManager' => [
                'eventManager',
                $this->getMockBuilder(EventManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\TestFixtureSubAbstractEntityRebuildLegacy::\$eventManager is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'hawkLogger' => [
                'hawkLogger',
                function (): object {
                    return clone $this->loggerFactoryMock->create();
                },
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\TestFixtureSubAbstractEntityRebuildLegacy::\$hawkLogger is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::hawkLogger has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via \$loggerFactory constructor injection.",
                ]
            ],
            'storeManager' => [
                'storeManager',
                $this->getMockBuilder(StoreManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\TestFixtureSubAbstractEntityRebuildLegacy::\$storeManager is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::storeManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'indexingContext' => [
                'indexingContext',
                $this->getMockBuilder(ContextInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\TestFixtureSubAbstractEntityRebuildLegacy::\$indexingContext is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\AbstractEntityRebuild::indexingContext has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ]
        ];
    }
}

class TestFixtureSubAbstractEntityRebuildLegacy extends AbstractEntityRebuild
{
    use AccessClassPropertyFixtureTrait;

    protected function isAllowedItem(DataObject $item): bool
    {
        return false;
    }

    protected function getEntityId(DataObject $entityItem): ?int
    {
        return null;
    }
}
