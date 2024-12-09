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

/**
 * ClientData Interface used in SearchRequest
 *
 * @api v2
 * @since 0.8.0
 * @see https://developerdocs.hawksearch.com/reference/searchv2_search-1
 * @see https://searchapi-dev.hawksearch.net/swagger/ui/index#!/SearchV2/SearchV2_Search
 */
interface ClientDataInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const FIELD_VISITOR_ID = 'VisitorId';
    const FIELD_VISIT_ID = 'VisitId';
    const FIELD_CUSTOM = 'Custom';
    const FIELD_EXTENDED_CUSTOM = 'ExtendedCustom';
    const FIELD_PREVIEW_BUCKETS = 'PreviewBuckets';
    const FIELD_SOURCE = 'Source';
    const FIELD_ORIGIN = 'Origin';
    const FIELD_ZIP_CODE = 'ZipCode';
    /**#@-*/

    /**
     * @return string
     */
    public function getVisitorId(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setVisitorId(?string $value): self;

    /**
     * @return string
     */
    public function getVisitId(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setVisitId(?string $value): self;

    /**
     * @return array
     */
    public function getCustom(): array;

    /**
     * @param array<string, string>|null $value
     * @return $this
     */
    public function setCustom(?array $value): self;

    /**
     * @return array
     */
    public function getExtendedCustom(): array;

    /**
     * @param array<string, list<string>>|null $value
     * @return $this
     */
    public function setExtendedCustom(?array $value): self;

    /**
     * @return array
     */
    public function getPreviewBuckets(): array;

    /**
     * @param list<int>|null $value
     * @return $this
     */
    public function setPreviewBuckets(?array $value): self;

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSource(?string $value): self;

    /**
     * @return \HawkSearch\EsIndexing\Api\Data\CoordinateInterface
     */
    public function getOrigin(): CoordinateInterface;

    /**
     * @param \HawkSearch\EsIndexing\Api\Data\CoordinateInterface|null $value
     * @return $this
     */
    public function setOrigin(?CoordinateInterface $value): self;

    /**
     * @return string
     */
    public function getZipCode(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setZipCode(?string $value): self;
}
