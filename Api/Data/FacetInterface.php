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

    public function getSyncGuid(): string;

    public function setSyncGuid(?string $value): self;

    public function getFacetId(): int;

    public function setFacetId(int $value): self;

    public function getName(): string;

    public function setName(?string $value): self;

    public function getFacetType(): string;

    public function setFacetType(?string $value): self;

    public function getFieldType(): string;

    public function setFieldType(?string $value): self;

    public function getMaxCount(): int;

    public function setMaxCount(int $value): self;

    public function getMinHitCount(): int;

    public function setMinHitCount(int $value): self;

    public function getField(): string;

    public function setField(?string $value): self;

    public function getParam(): string;

    public function setParam(?string $value): self;

    public function getDisplayType(): string;

    public function setDisplayType(?string $value): self;

    public function getScrollHeight(): int;

    public function setScrollHeight(int $value): self;

    public function getScrollThreshold(): int;

    public function setScrollThreshold(int $value): self;

    public function getTruncateThreshold(): int;

    public function setTruncateThreshold(int $value): self;

    public function getSearchThreshold(): int;

    public function setSearchThreshold(int $value): self;

    public function getSortOrder(): int;

    public function setSortOrder(int $value): self;

    public function getExpandSelection(): bool;

    public function setExpandSelection(bool $value): self;

    public function getIsCurrency(): bool;

    public function setIsCurrency(bool $value): self;

    public function getIsNumeric(): bool;

    public function setIsNumeric(bool $value): self;

    public function getIsSearch(): bool;

    public function setIsSearch(bool $value): self;

    public function getIsVisible(): bool;

    public function setIsVisible(bool $value): self;

    public function getUBound(): string;

    public function setUBound(?string $value): self;

    public function getLBound(): string;

    public function setLBound(?string $value): self;

    public function getIncrement(): string;

    public function setIncrement(?string $value): self;

    public function getNofVisible(): int;

    public function setNofVisible(int $value): self;

    public function getHeight(): int;

    public function setHeight(int $value): self;

    public function getDisplayRuleXML(): string;

    public function setDisplayRuleXML(?string $value): self;

    public function getSortBy(): string;

    public function setSortBy(?string $value): self;

    public function getParentId(): int;

    public function setParentId(int $value): self;

    public function getIsCollapsible(): bool;

    public function setIsCollapsible(bool $value): self;

    public function getIsCollapsedDefault(): bool;

    public function setIsCollapsedDefault(bool $value): self;

    public function getSwatchData(): string;

    public function setSwatchData(?string $value): self;

    public function getFacetRangeDisplayType(): int;

    public function setFacetRangeDisplayType(int $value): self;

    public function getPreloadChildren(): bool;

    public function setPreloadChildren(bool $value): self;

    public function getTooltip(): string;

    public function setTooltip(?string $value): self;

    public function getShowSliderInputs(): bool;

    public function setShowSliderInputs(bool $value): self;

    public function getShowFacetImageCount(): bool;

    public function setShowFacetImageCount(bool $value): self;

    /**
     * @return FacetRangeModelInterface[]
     */
    public function getFacetRanges(): array;

    /**
     * @param FacetRangeModelInterface[]|null $value
     */
    public function setFacetRanges(?array $value): self;

    public function getTags(): string;

    public function setTags(?string $value): self;

    public function getCreateDate(): string;

    public function setCreateDate(?string $value): self;

    public function getModifyDate(): string;

    public function setModifyDate(?string $value): self;

    public function getBoostBury(): FacetBoostBuryInterface;
    
    public function setBoostBury(?FacetBoostBuryInterface $value): self;

    public function getListName(): string;

    public function setListName(?string $value): self;

    public function getNumericPrecision(): int;

    public function setNumericPrecision(int $value): self;

    public function getCurrencySymbol(): string;

    public function setCurrencySymbol(?string $value): self;

    public function getDefaultItemType(): string;

    public function setDefaultItemType(?string $value): self;
}
