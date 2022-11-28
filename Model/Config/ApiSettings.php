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
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @deprecated 0.1.3 No longer used
 * @see \HawkSearch\Connector\Model\Config\ApiSettings
 */
class ApiSettings extends ConnectorApiSettings
{
    /**#@+
     * Configuration paths
     */
    const INDEXING_API_URL = 'indexing_api_url';
    const SEARCH_API_URL = 'search_api_url';
    /**#@-*/

    /**
     * ApiSettings constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param null $configRootPath
     * @param null $configGroup
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $configRootPath = null,
        $configGroup = null
    )
    {
        parent::__construct($scopeConfig, $configRootPath, $configGroup);
    }
}
