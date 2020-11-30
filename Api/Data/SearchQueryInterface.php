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

namespace HawkSearch\EsIndexing\Api\Data;

interface SearchQueryInterface
{
    /**#@+
     * Constants for keys of data array
     */
    public const FIELD_CLIENT_GUID = 'ClientGuid';
    public const FIELD_KEYWORD = 'Keyword';
    public const FIELD_CUSTOM_URL = 'CustomUrl';
    public const FIELD_PAGE_NO = 'PageNo';
    public const FIELD_MAX_PER_PAGE = 'MaxPerPage';
    public const FIELD_SORT_BY = 'SortBy';
    public const FIELD_SORTING_SET_CODE = 'SortingSetCode';
    public const FIELD_SEARCH_WITHIN = 'SearchWithin';
    public const FIELD_FACET_SELECTIONS = 'FacetSelections';
    public const FIELD_FACET_OVERRIDE = 'FacetOverride';
    public const FIELD_FIELD_OVERRIDE = 'FieldOverride';
    public const FIELD_CLIENT_DATA = 'ClientData';
    public const FIELD_IS_100_COVERAGE_TURNED_ON = 'Is100CoverageTurnedOn';
    /**#@-*/

    /**
     * @return string|null
     */
    public function getClientGuid(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setClientGuid(string $value);

    /**
     * @return string
     */
    public function getKeyword(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setKeyword(string $value);

    /**
     * @return string|null
     */
    public function getCustomUrl(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomUrl(string $value);

    /**
     * @return int
     */
    public function getPageNo(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setPageNo(int $value);

    /**
     * @return int
     */
    public function getMaxPerPage(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setMaxPerPage(int $value);

    /**
     * @return string|null
     */
    public function getSortBy(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setSortBy(string $value);

    /**
     * @return string
     */
    public function getSortingSetCode(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setSortingSetCode(string $value);

    /**
     * @return string
     */
    public function getSearchWithin(): ?string;

    /**
     * @param string $value
     * @return $this
     */
    public function setSearchWithin(string $value);

    /**
     * @return FacetSelectionsInterface
     */
    public function getFacetSelections(): FacetSelectionsInterface;

    /**
     * @param FacetSelectionsInterface $value
     * @return $this
     */
    public function setFacetSelections(FacetSelectionsInterface $value);

    /**
     * @return string[]
     */
    public function getFacetOverride(): array;

    /**
     * @param string[] $value
     * @return $this
     */
    public function setFacetOverride(array $value);

    /**
     * @return string[]
     */
    public function getFieldOverride(): array;

    /**
     * @param string[] $value
     * @return $this
     */
    public function setFieldOverride(array $value);

    /**
     * @return ClientDataInterface
     */
    public function getClientData(): ClientDataInterface;

    /**
     * @param ClientDataInterface $value
     * @return $this
     */
    public function setClientData(ClientDataInterface $value);

    /**
     * @return bool
     */
    public function is100CoverageTurnedOn(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIs100CoverageTurnedOn(bool $value);
}
