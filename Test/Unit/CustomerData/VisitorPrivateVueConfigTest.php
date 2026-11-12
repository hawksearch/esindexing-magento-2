<?php
/**
 * Copyright (c) 2026 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Test\Unit\CustomerData;

use HawkSearch\Connector\Model\RemoteAddressResolverInterface;
use HawkSearch\EsIndexing\CustomerData\VisitorPrivateVueConfig;
use PHPUnit\Framework\TestCase;

class VisitorPrivateVueConfigTest extends TestCase
{
    /**
     * @dataProvider addressProvider
     */
    public function testSectionReturnsResolvedAddress(?string $address): void
    {
        $resolver = $this->createMock(RemoteAddressResolverInterface::class);
        $resolver->expects($this->once())->method('resolve')->willReturn($address);

        $section = new VisitorPrivateVueConfig($resolver);

        $this->assertSame(['config' => ['clientRemoteAddress' => $address]], $section->getSectionData());
    }

    public function addressProvider(): array
    {
        return [
            ['198.51.100.42'],
            [null],
        ];
    }
}
