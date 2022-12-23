<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Api\Data;

interface ClientDataInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const FIELD_VISITOR_ID = 'VisitorId';
    const FIELD_CUSTOM = 'Custom';
    const FIELD_HTTP_TRUE_CLIENT_IP = 'HttpTrueClientIp';
    const FIELD_USER_AGENT = 'UserAgent';
    const FIELD_SOURCE = 'Source';
    /**#@-*/

    /**
     * @return string
     */
    public function getVisitorId(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setVisitorId(string $value);

    /**
     * @return ClientDataCustomInterface
     */
    public function getCustom(): ClientDataCustomInterface;

    /**
     * @param ClientDataCustomInterface $value
     * @return $this
     */
    public function setCustom(ClientDataCustomInterface $value);

    /**
     * @return string
     */
    public function getHttpTrueClientIp(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setHttpTrueClientIp(string $value);

    /**
     * @return string
     */
    public function getUserAgent(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setUserAgent(string $value);

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setSource(string $value);
}
