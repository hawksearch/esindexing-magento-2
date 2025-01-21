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
use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract;
use HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\MessageManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ActionAbstractTest extends TestCase
{
    use LegacyBaseTrait;

    private ManagerInterface|MockObject $eventManagerMock;
    private MessageManagerInterface|MockObject $messageManagerMock;
    private MockObject|BulkPublisherInterface $publisherMock;
    private MockObject|SchedulerInterface $entitySchedulerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);

        $this->eventManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->messageManagerMock = $this->getMockBuilder(MessageManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->publisherMock = $this->getMockBuilder(BulkPublisherInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->entitySchedulerMock = $this->getMockBuilder(SchedulerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
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
        $model = new TestFixtureSubActionAbstractLegacy(
            $this->eventManagerMock,
            $this->messageManagerMock,
            $this->publisherMock,
            $this->entitySchedulerMock
        );

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp81(): array
    {
        return [
            'eventManager' => [
                'eventManager',
                $this->getMockBuilder(ManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'messageManager' => [
                'messageManager',
                $this->getMockBuilder(MessageManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'publisher' => [
                'publisher',
                $this->getMockBuilder(BulkPublisherInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'entityScheduler' => [
                'entityScheduler',
                $this->getMockBuilder(SchedulerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
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
        $model = new TestFixtureSubActionAbstractLegacy(
            $this->eventManagerMock,
            $this->messageManagerMock,
            $this->publisherMock,
            $this->entitySchedulerMock
        );

        $this->assertLegacyProperty($property, $newPropertyValue, $model, $this, $deprecationsTriggered);
    }

    public function provideLegacyPropertiesPhp82(): array
    {
        return [
            'eventManager' => [
                'eventManager',
                $this->getMockBuilder(ManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities\TestFixtureSubActionAbstractLegacy::\$eventManager is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::eventManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'messageManager' => [
                'messageManager',
                $this->getMockBuilder(MessageManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities\TestFixtureSubActionAbstractLegacy::\$messageManager is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::messageManager has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'publisher' => [
                'publisher',
                $this->getMockBuilder(BulkPublisherInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities\TestFixtureSubActionAbstractLegacy::\$publisher is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::publisher has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],
            'entityScheduler' => [
                'entityScheduler',
                $this->getMockBuilder(SchedulerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass(),
                [
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexer\Entities\TestFixtureSubActionAbstractLegacy::\$entityScheduler is deprecated",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                    "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract::entityScheduler has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
                ]
            ],

        ];
    }
}

/**
 * @group legacy
 */
class TestFixtureSubActionAbstractLegacy extends ActionAbstract
{
    use AccessClassPropertyFixtureTrait;

    public function execute(array $ids): self
    {
        return $this;
    }
}
