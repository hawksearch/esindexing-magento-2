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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Indexing\FieldHandler;

use HawkSearch\Connector\Test\Unit\Compatibility\Fixtures\AccessClassPropertyFixtureTrait;
use HawkSearch\Connector\Test\Unit\Compatibility\LegacyBaseTrait;
use HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite;
use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;

class CompositeTest extends TestCase
{
    use LegacyBaseTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy($this);
    }

    protected function tearDown(): void
    {
        $this->tearDownLegacy($this);
        parent::tearDown();
    }

    /**
     * @requires PHP <8.2.0
     * @group legacy
     */
    #[RequiresPhp('<8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp81(): void
    {
        $model = new TestFixtureSubCompositeLegacy(ObjectManager::getInstance(), []);

        $this->assertLegacyProperty('handlers', [], $model, $this, [
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
        ]);
    }

    /**
     * @requires PHP >=8.2.0
     * @group legacy
     */
    #[RequiresPhp('>=8.2.0')]
    public function testAccessingDeprecatedPropertiesPhp82(): void
    {
        $model = new TestFixtureSubCompositeLegacy(ObjectManager::getInstance(), []);

        $this->assertLegacyProperty('handlers', [], $model, $this, [
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Creation of dynamic property via __set(): HawkSearch\EsIndexing\Test\Unit\Model\Indexing\FieldHandler\TestFixtureSubCompositeLegacy::\$handlers is deprecated",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
            "Since 0.8.0: Property HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite::handlers has been deprecated and it's public/protected usage will be discontinued. Visibility changed to private. Set via constructor injection.",
        ]);
    }
}

/**
 * @group legacy
 */
class TestFixtureSubCompositeLegacy extends Composite
{
    use AccessClassPropertyFixtureTrait;
}
