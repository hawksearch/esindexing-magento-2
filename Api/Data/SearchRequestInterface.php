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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * SearchRequest Interface for Search API v2
 *
 * @api v2
 * @since 0.8.0
 * @link https://developerdocs.hawksearch.com/reference/searchv2_search-1
 * @link https://searchapi-dev.hawksearch.net/swagger/ui/index#!/SearchV2/SearchV2_Search
 */
interface SearchRequestInterface
{
    public const FIELD_INDEX_NAME = 'IndexName';
    public const FIELD_QUERY = 'Query';
    public const FIELD_VARIANT = 'Variant';
    public const FIELD_BOOST_QUERIES = 'BoostQueries';
    public const FIELD_DISTANCE_UNIT_TYPE = 'DistanceUnitType';
    public const FIELD_REQUEST_TYPE = 'RequestType';
    public const FIELD_IMAGE_DATA = 'ImageData';
    public const FIELD_IMAGE_TEXT = 'ImageText';
    public const FIELD_K_VALUE = 'KValue';
    public const FIELD_CLIENT_GUID = 'ClientGuid';
    public const FIELD_KEYWORD = 'Keyword';
    public const FIELD_PAGE_ID = 'PageId';
    public const FIELD_PAGE_NO = 'PageNo';
    public const FIELD_MAX_PER_PAGE = 'MaxPerPage';
    public const FIELD_SEARCH_WITHIN = 'SearchWithin';
    public const FIELD_SORT_BY = 'SortBy';
    public const FIELD_SORTING_SET_CODE = 'SortingSetCode';
    public const FIELD_PAGINATION_SET_CODE = 'PaginationSetCode';
    public const FIELD_FACET_SELECTIONS = 'FacetSelections';
    public const FIELD_CUSTOM_URL = 'CustomUrl';
    public const FIELD_IS_IN_PREVIEW = 'IsInPreview';
    public const FIELD_IS_100_COVERAGE_TURNED_ON = 'Is100CoverageTurnedOn';
    public const FIELD_EXPLAIN_DOC_ID = 'ExplainDocId';
    public const FIELD_FACET_OVERRIDE = 'FacetOverride';
    public const FIELD_FIELD_OVERRIDE = 'FieldOverride';
    public const FIELD_SMART_BAR = 'SmartBar';
    public const FIELD_CLIENT_DATA = 'ClientData';
    public const FIELD_SEARCH_TYPE = 'SearchType';
    public const FIELD_IGNORE_SPELLCHECK = 'IgnoreSpellcheck';

    /**
     * @return string
     */
    public function getIndexName(): string;

    /**
     * @return $this
     */
    public function setIndexName(?string $value): self;

    /**
     * @return string
     */
    public function getQuery(): string;

    /**
     * @return $this
     */
    public function setQuery(?string $value): self;


    /**
     * @return VariantOptionsInterface
     */
    public function getVariant(): VariantOptionsInterface;


    /**
     * @return $this
     */
    public function setVariant(?VariantOptionsInterface $value): self;

    /**
     * @return BoostQueryInterface[]
     */
    public function getBoostQueries(): array;

    /**
     * @param BoostQueryInterface[]|null $value
     * @return $this
     */
    public function setBoostQueries(?array $value): self;

    /**
     * @return int
     */
    public function getDistanceUnitType(): int;

    /**
     * @return $this
     */
    public function setDistanceUnitType(int $value): self;

    /**
     * @return int
     */
    public function getRequestType(): int;

    /**
     * @return $this
     */
    public function setRequestType(int $value): self;

    /**
     * @return string
     */
    public function getImageData(): string;

    /**
     * @return $this
     */
    public function setImageData(?string $value): self;

    /**
     * @return string
     */
    public function getImageText(): string;

    /**
     * @return $this
     */
    public function setImageText(?string $value): self;

    /**
     * @return int
     */
    public function getKValue(): int;

    /**
     * @return $this
     */
    public function setKValue(int $value): self;

    /**
     * @return string
     */
    public function getClientGuid(): string;

    /**
     * @return $this
     */
    public function setClientGuid(?string $value): self;

    /**
     * @return string
     */
    public function getKeyword(): string;

    /**
     * @return $this
     */
    public function setKeyword(?string $value): self;

    /**
     * @return int
     */
    public function getPageId(): int;

    /**
     * @return $this
     */
    public function setPageId(int $value): self;

    /**
     * @return int
     */
    public function getPageNo(): int;

    /**
     * @return $this
     */
    public function setPageNo(int $value): self;

    /**
     * @return int
     */
    public function getMaxPerPage(): int;

    /**
     * @return $this
     */
    public function setMaxPerPage(int $value): self;

    /**
     * @return string
     */
    public function getSearchWithin(): string;

    /**
     * @return $this
     */
    public function setSearchWithin(?string $value): self;

    /**
     * @return string
     */
    public function getSortBy(): string;

    /**
     * @return $this
     */
    public function setSortBy(?string $value): self;

    /**
     * @return string
     */
    public function getSortingSetCode(): string;

    /**
     * @return $this
     */
    public function setSortingSetCode(?string $value): self;

    /**
     * @return string
     */
    public function getPaginationSetCode(): string;

    /**
     * @return $this
     */
    public function setPaginationSetCode(?string $value): self;

    /**
     * @return array<string, list<array<string, string>>>
     */
    public function getFacetSelections(): array;

    /**
     * @param array<string, list<array<string, string>>>|null $value
     * @return $this
     */
    public function setFacetSelections(?array $value): self;

    /**
     * @return string
     */
    public function getCustomUrl(): string;

    /**
     * @return $this
     */
    public function setCustomUrl(?string $value): self;

    /**
     * @return bool
     */
    public function getIsInPreview(): bool;

    /**
     * @return $this
     */
    public function setIsInPreview(bool $value): self;

    /**
     * @return bool
     */
    public function getIs100CoverageTurnedOn(): bool;

    /**
     * @return $this
     */
    public function setIs100CoverageTurnedOn(bool $value): self;

    /**
     * @return string
     */
    public function getExplainDocId(): string;

    /**
     * @return $this
     */
    public function setExplainDocId(?string $value): self;

    /**
     * @return string[]
     */
    public function getFacetOverride(): array;

    /**
     * @param string[]|null $value
     * @return $this
     */
    public function setFacetOverride(?array $value): self;

    /**
     * @return string[]
     */
    public function getFieldOverride(): array;

    /**
     * @param string[]|null $value
     * @return $this
     */
    public function setFieldOverride(?array $value): self;

    /**
     * @return SmartBarInterface
     */
    public function getSmartBar(): SmartBarInterface;

    /**
     * @return $this
     */
    public function setSmartBar(?SmartBarInterface $value): self;

    /**
     * @return ClientDataInterface
     */
    public function getClientData(): ClientDataInterface;

    /**
     * @return $this
     */
    public function setClientData(?ClientDataInterface $value): self;

    /**
     * @return string
     */
    public function getSearchType(): string;

    /**
     * @return $this
     */
    public function setSearchType(?string $value): self;

    /**
     * @return bool
     */
    public function getIgnoreSpellcheck(): bool;

    /**
     * @return $this
     */
    public function setIgnoreSpellcheck(bool $value): self;
}
