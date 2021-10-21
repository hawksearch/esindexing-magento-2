<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Gateway\Config;

use HawkSearch\Connector\Gateway\Config\ApiConfigDefault;
use HawkSearch\Connector\Model\Config\ApiSettings as ConnectorApiSettingsProvider;
use HawkSearch\EsIndexing\Model\Config\ApiSettings as ApiSettingsProvider;

class EsIndexingApiConfig extends ApiConfigDefault
{
    /**
     * @var ApiSettingsProvider
     */
    private $apiSettingsProvider;

    /**
     * ApiConfigDefault constructor.
     * @param ApiSettingsProvider $apiSettingsProvider
     * @param ConnectorApiSettingsProvider $connectorApiSettingsProvider
     */
    public function __construct(
        ApiSettingsProvider $apiSettingsProvider,
        ConnectorApiSettingsProvider $connectorApiSettingsProvider
    ) {
        parent::__construct($connectorApiSettingsProvider);
        $this->apiSettingsProvider = $apiSettingsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getApiUrl(): string
    {
        return $this->apiSettingsProvider->getIndexingApiUrl();
    }
}
