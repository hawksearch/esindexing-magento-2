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
 * Facet Interface used in HawkSearch API
 *
 * @api v11
 * @link https://developerdocs.hawksearch.com/reference/facet_post_value
 * @link https://dev.hawksearch.net/swagger/ui/index#!/Facet/Facet_Post_value
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FacetInterface
{
    const SYNC_GUID = 'SyncGuid';
    const FACET_ID = 'FacetId';
    const NAME = 'Name';
    const FACET_TYPE = 'FacetType';
    const FIELD_TYPE = 'FieldType';
    const MAX_COUNT = 'MaxCount';
    const MIN_HIT_COUNT = 'MinHitCount';
    const FIELD = 'Field';
    const PARAM = 'Param';
    const DISPLAY_TYPE = 'DisplayType';
    const SCROLL_HEIGHT = 'ScrollHeight';
    const SCROLL_THRESHOLD = 'ScrollThreshold';
    const TRUNCATE_THRESHOLD = 'TruncateThreshold';
    const SEARCH_THRESHOLD = 'SearchThreshold';
    const SORT_ORDER = 'SortOrder';
    const EXPAND_SELECTION = 'ExpandSelection';
    const IS_CURRENCY = 'IsCurrency';
    const IS_NUMERIC = 'IsNumeric';
    const IS_SEARCH = 'IsSearch';
    const IS_VISIBLE = 'IsVisible';
    const U_BOUND = 'UBound';
    const L_BOUND = 'LBound';
    const INCREMENT = 'Increment';
    const NOF_VISIBLE = 'NofVisible';
    const HEIGHT = 'Height';
    const DISPLAY_RULE_XML = 'DisplayRuleXML';
    const SORT_BY = 'SortBy';
    const PARENT_ID = 'ParentId';
    const IS_COLLAPSIBLE = 'IsCollapsible';
    const IS_COLLAPSED_DEFAULT = 'IsCollapsedDefault';
    const SWATCH_DATA = 'SwatchData';
    const FACET_RANGE_DISPLAY_TYPE = 'FacetRangeDisplayType';
    const PRELOAD_CHILDREN = 'PreloadChildren';
    const TOOLTIP = 'Tooltip';
    const SHOW_SLIDER_INPUTS = 'ShowSliderInputs';
    const SHOW_FACET_IMAGE_COUNT = 'ShowFacetImageCount';
    const FACET_RANGES = 'FacetRanges';
    const TAGS = 'Tags';
    const CREATE_DATE = 'CreateDate';
    const MODIFY_DATE = 'ModifyDate';
    const BOOST_BURY = 'BoostBury';
    const LIST_NAME = 'ListName';
    const NUMERIC_PRECISION = 'NumericPrecision';
    const CURRENCY_SYMBOL = 'CurrencySymbol';
    const DEFAULT_ITEM_TYPE = 'DefaultItemType';

    /**
     * @return string
     */
    public function getSyncGuid(): string;

    /**
     * @return $this
     */
    public function setSyncGuid(?string $value): self;

    /**
     * @return int
     */
    public function getFacetId(): int;

    /**
     * @return $this
     */
    public function setFacetId(int $value): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return $this
     */
    public function setName(?string $value): self;

    /**
     * @return string
     */
    public function getFacetType(): string;

    /**
     * @return $this
     */
    public function setFacetType(?string $value): self;

    /**
     * @return string
     */
    public function getFieldType(): string;

    /**
     * @return $this
     */
    public function setFieldType(?string $value): self;

    /**
     * @return int
     */
    public function getMaxCount(): int;

    /**
     * @return $this
     */
    public function setMaxCount(int $value): self;

    /**
     * @return int
     */
    public function getMinHitCount(): int;

    /**
     * @return $this
     */
    public function setMinHitCount(int $value): self;

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @return $this
     */
    public function setField(?string $value): self;

    /**
     * @return string
     */
    public function getParam(): string;

    /**
     * @return $this
     */
    public function setParam(?string $value): self;

    /**
     * @return string
     */
    public function getDisplayType(): string;

    /**
     * @return $this
     */
    public function setDisplayType(?string $value): self;

    /**
     * @return int
     */
    public function getScrollHeight(): int;

    /**
     * @return $this
     */
    public function setScrollHeight(int $value): self;

    /**
     * @return int
     */
    public function getScrollThreshold(): int;

    /**
     * @return $this
     */
    public function setScrollThreshold(int $value): self;

    /**
     * @return int
     */
    public function getTruncateThreshold(): int;

    /**
     * @return $this
     */
    public function setTruncateThreshold(int $value): self;

    /**
     * @return int
     */
    public function getSearchThreshold(): int;

    /**
     * @return $this
     */
    public function setSearchThreshold(int $value): self;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @return $this
     */
    public function setSortOrder(int $value): self;

    /**
     * @return bool
     */
    public function getExpandSelection(): bool;

    /**
     * @return $this
     */
    public function setExpandSelection(bool $value): self;

    /**
     * @return bool
     */
    public function getIsCurrency(): bool;

    /**
     * @return $this
     */
    public function setIsCurrency(bool $value): self;

    /**
     * @return bool
     */
    public function getIsNumeric(): bool;

    /**
     * @return $this
     */
    public function setIsNumeric(bool $value): self;

    /**
     * @return bool
     */
    public function getIsSearch(): bool;

    /**
     * @return $this
     */
    public function setIsSearch(bool $value): self;

    /**
     * @return bool
     */
    public function getIsVisible(): bool;

    /**
     * @return $this
     */
    public function setIsVisible(bool $value): self;

    /**
     * @return string
     */
    public function getUBound(): string;

    /**
     * @return $this
     */
    public function setUBound(?string $value): self;

    /**
     * @return string
     */
    public function getLBound(): string;

    /**
     * @return $this
     */
    public function setLBound(?string $value): self;

    /**
     * @return string
     */
    public function getIncrement(): string;

    /**
     * @return $this
     */
    public function setIncrement(?string $value): self;

    /**
     * @return int
     */
    public function getNofVisible(): int;

    /**
     * @return $this
     */
    public function setNofVisible(int $value): self;

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @return $this
     */
    public function setHeight(int $value): self;

    /**
     * @return string
     */
    public function getDisplayRuleXML(): string;

    /**
     * @return $this
     */
    public function setDisplayRuleXML(?string $value): self;

    /**
     * @return string
     */
    public function getSortBy(): string;

    /**
     * @return $this
     */
    public function setSortBy(?string $value): self;

    /**
     * @return int
     */
    public function getParentId(): int;

    /**
     * @return $this
     */
    public function setParentId(int $value): self;

    /**
     * @return bool
     */
    public function getIsCollapsible(): bool;

    /**
     * @return $this
     */
    public function setIsCollapsible(bool $value): self;

    /**
     * @return bool
     */
    public function getIsCollapsedDefault(): bool;

    /**
     * @return $this
     */
    public function setIsCollapsedDefault(bool $value): self;

    /**
     * @return string
     */
    public function getSwatchData(): string;

    /**
     * @return $this
     */
    public function setSwatchData(?string $value): self;

    /**
     * @return int
     */
    public function getFacetRangeDisplayType(): int;

    /**
     * @return $this
     */
    public function setFacetRangeDisplayType(int $value): self;

    /**
     * @return bool
     */
    public function getPreloadChildren(): bool;

    /**
     * @return $this
     */
    public function setPreloadChildren(bool $value): self;

    /**
     * @return string
     */
    public function getTooltip(): string;

    /**
     * @return $this
     */
    public function setTooltip(?string $value): self;

    /**
     * @return bool
     */
    public function getShowSliderInputs(): bool;

    /**
     * @return $this
     */
    public function setShowSliderInputs(bool $value): self;

    /**
     * @return bool
     */
    public function getShowFacetImageCount(): bool;

    /**
     * @return $this
     */
    public function setShowFacetImageCount(bool $value): self;

    /**
     * @return FacetRangeModelInterface[]
     */
    public function getFacetRanges(): array;

    /**
     * @param FacetRangeModelInterface[]|null $value
     * @return $this
     */
    public function setFacetRanges(?array $value): self;

    /**
     * @return string
     */
    public function getTags(): string;

    /**
     * @return $this
     */
    public function setTags(?string $value): self;

    /**
     * @return string
     */
    public function getCreateDate(): string;

    /**
     * @return $this
     */
    public function setCreateDate(?string $value): self;

    /**
     * @return string
     */
    public function getModifyDate(): string;

    /**
     * @return $this
     */
    public function setModifyDate(?string $value): self;

    /**
     * @return FacetBoostBuryInterface
     */
    public function getBoostBury(): FacetBoostBuryInterface;

    /**
     * @return $this
     */
    public function setBoostBury(?FacetBoostBuryInterface $value): self;

    /**
     * @return string
     */
    public function getListName(): string;

    /**
     * @return $this
     */
    public function setListName(?string $value): self;

    /**
     * @return int
     */
    public function getNumericPrecision(): int;

    /**
     * @return $this
     */
    public function setNumericPrecision(int $value): self;

    /**
     * @return string
     */
    public function getCurrencySymbol(): string;

    /**
     * @return $this
     */
    public function setCurrencySymbol(?string $value): self;

    /**
     * @return string
     */
    public function getDefaultItemType(): string;

    /**
     * @return $this
     */
    public function setDefaultItemType(?string $value): self;
}
