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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Config;

use HawkSearch\EsIndexing\Model\Config\Products;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductsTest extends TestCase
{
    private ScopeConfigInterface|MockObject $scopeConfigMock;
    private Products $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->model = new Products($this->scopeConfigMock);
    }

    public function testGetAttributesWithNullConfig(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')->willReturn(null);

        $this->assertEquals('{}', $this->model->getAttributes());
    }

    public function testGetAttributes(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')->willReturn('attributes value');

        $this->assertEquals('attributes value', $this->model->getAttributes());
    }
}
