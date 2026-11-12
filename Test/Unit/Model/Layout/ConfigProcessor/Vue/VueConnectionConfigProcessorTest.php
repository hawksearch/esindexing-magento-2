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

namespace HawkSearch\EsIndexing\Test\Unit\Model\Layout\ConfigProcessor\Vue;

use HawkSearch\EsIndexing\Model\Config\ApiSettings;
use HawkSearch\EsIndexing\Model\Layout\ConfigProcessor\Vue\VueConnectionConfigProcessor;
use PHPUnit\Framework\TestCase;

class VueConnectionConfigProcessorTest extends TestCase
{
    public function testProcessedConfigDoesNotContainVisitorAddress(): void
    {
        $apiSettings = $this->createMock(ApiSettings::class);
        $apiSettings->method('getSearchApiUrl')->willReturn('https://search.example');
        $apiSettings->method('getHawksearchWorkbenchUrl')->willReturn('https://workbench.example');
        $apiSettings->method('getClientGuid')->willReturn('client-guid');
        $apiSettings->method('getTrackingUrl')->willReturn('https://tracking.example/');

        $processor = new VueConnectionConfigProcessor($apiSettings);
        $config = $processor->process([]);

        $this->assertArrayNotHasKey('clientRemoteAddress', $config);
    }
}
