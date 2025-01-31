<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Config\Indexing;

use HawkSearch\Connector\Model\ConfigProvider;

class FailureRecovery extends ConfigProvider
{
    private const CONFIG_ENABLE = 'enable';
    private const CONFIG_MAXIMUM_RETRIES = 'maximum_retries';
    private const CONFIG_MAXIMUM_OPEN_DELAY = 'maximum_open_delay';
    private const CONFIG_CRON_EXPR = 'cron_expr';

    /**
     * Default maximum retry attempts.
     */
    private const MAXIMUM_RETRIES_DEFAULT = 3;

    /**
     * Default maximum holding time of incomplete events. Default is 12h.
     */
    private const MAXIMUM_OPEN_DELAY_DEFAULT = 43200;

    /**
     * Check if Failure Recovery automation cron is enabled
     *
     * @param null|int|string $store
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    public function isEnabled($store = null): bool
    {
        return !!$this->getConfig(self::CONFIG_ENABLE, $store);
    }

    /**
     * Return the maximum number of times to retry processing an event after an error occurred
     *
     * @param null|int|string $store
     * @return int
     * @noinspection PhpMissingParamTypeInspection
     */
    public function getMaximumRetries($store = null): int
    {
        return (int)$this->getConfig(self::CONFIG_MAXIMUM_RETRIES, $store) ?: static::MAXIMUM_RETRIES_DEFAULT;
    }

    /**
     * Return maximum holding time for incomplete events
     *
     * @param null|int|string $store
     * @return int
     * @noinspection PhpMissingParamTypeInspection
     */
    public function getMaximumOpenDelay($store = null): int
    {
        return (int)$this->getConfig(self::CONFIG_MAXIMUM_OPEN_DELAY, $store) ?: static::MAXIMUM_OPEN_DELAY_DEFAULT;
    }
}
