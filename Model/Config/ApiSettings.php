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

namespace HawkSearch\EsIndexing\Model\Config;

use HawkSearch\Connector\Model\Config\ApiSettings as ConnectorApiSettings;
use HawkSearch\Connector\Model\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ApiSettings extends ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    const API_URL = 'hawk_url';
    /**#@-*/

    /**
     * @var ConnectorApiSettings
     */
    private $connectorApiSettings;

    /**
     * ApiSettings constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ConnectorApiSettings $connectorApiSettings
     * @param null $configRootPath
     * @param null $configGroup
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConnectorApiSettings $connectorApiSettings,
        $configRootPath = null,
        $configGroup = null
    )
    {
        parent::__construct($scopeConfig, $configRootPath, $configGroup);
        $this->connectorApiSettings = $connectorApiSettings;
    }

    /**
     * @param null|int|string $store
     * @return string | null
     */
    public function getApiUrl($store = null) : ?string
    {
        return 'https://indexing-dev.hawksearch.net/';
//        return $this->getConfig(self::API_URL . '/' . $this->connectorApiSettings->getApiMode(), $store);
    }
}
