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

    public function getIndexName(): string;

    public function setIndexName(?string $value): self;

    public function getQuery(): string;

    public function setQuery(?string $value): self;


    public function getVariant(): VariantOptionsInterface;


    public function setVariant(?VariantOptionsInterface $value): self;

    /**
     * @return BoostQueryInterface[]
     */
    public function getBoostQueries(): array;

    /**
     * @param BoostQueryInterface[]|null $value
     */
    public function setBoostQueries(?array $value): self;

    public function getDistanceUnitType(): int;

    public function setDistanceUnitType(int $value): self;

    public function getRequestType(): int;

    public function setRequestType(int $value): self;

    public function getImageData(): string;

    public function setImageData(?string $value): self;

    public function getImageText(): string;

    public function setImageText(?string $value): self;

    public function getKValue(): int;

    public function setKValue(int $value): self;

    public function getClientGuid(): string;

    public function setClientGuid(?string $value): self;

    public function getKeyword(): string;

    public function setKeyword(?string $value): self;

    public function getPageId(): int;

    public function setPageId(int $value): self;

    public function getPageNo(): int;

    public function setPageNo(int $value): self;

    public function getMaxPerPage(): int;

    public function setMaxPerPage(int $value): self;

    public function getSearchWithin(): string;

    public function setSearchWithin(?string $value): self;

    public function getSortBy(): string;

    public function setSortBy(?string $value): self;

    public function getSortingSetCode(): string;

    public function setSortingSetCode(?string $value): self;

    public function getPaginationSetCode(): string;

    public function setPaginationSetCode(?string $value): self;

    /**
     * @return array<string, list<array<string, string>>>
     */
    public function getFacetSelections(): array;

    /**
     * @param array<string, list<array<string, string>>>|null $value
     */
    public function setFacetSelections(?array $value): self;

    public function getCustomUrl(): string;

    public function setCustomUrl(?string $value): self;

    public function getIsInPreview(): bool;

    public function setIsInPreview(bool $value): self;

    public function getIs100CoverageTurnedOn(): bool;

    public function setIs100CoverageTurnedOn(bool $value): self;

    public function getExplainDocId(): string;

    public function setExplainDocId(?string $value): self;

    /**
     * @return string[]
     */
    public function getFacetOverride(): array;

    /**
     * @param string[]|null $value
     */
    public function setFacetOverride(?array $value): self;

    /**
     * @return string[]
     */
    public function getFieldOverride(): array;

    /**
     * @param string[]|null $value
     */
    public function setFieldOverride(?array $value): self;

    public function getSmartBar(): SmartBarInterface;

    public function setSmartBar(?SmartBarInterface $value): self;

    public function getClientData(): ClientDataInterface;

    public function setClientData(?ClientDataInterface $value): self;

    public function getSearchType(): string;

    public function setSearchType(?string $value): self;

    public function getIgnoreSpellcheck(): bool;

    public function setIgnoreSpellcheck(bool $value): self;
}
