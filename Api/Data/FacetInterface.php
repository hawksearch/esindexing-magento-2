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
    /**#@+
     * Constants for keys of data array
     */
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
    /**#@-*/

    /**
     * @return string
     */
    public function getSyncGuid() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSyncGuid(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getFacetId() : int;

    /**
     * @return $this
     */
    public function setFacetId(int $value): FacetInterface;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setName(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getFacetType() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setFacetType(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getFieldType() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setFieldType(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getMaxCount() : int;

    /**
     * @return $this
     */
    public function setMaxCount(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getMinHitCount() : int;

    /**
     * @return $this
     */
    public function setMinHitCount(int $value): FacetInterface;

    /**
     * @return string
     */
    public function getField() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setField(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getParam() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setParam(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getDisplayType() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setDisplayType(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getScrollHeight() : int;

    /**
     * @return $this
     */
    public function setScrollHeight(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getScrollThreshold() : int;

    /**
     * @return $this
     */
    public function setScrollThreshold(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getTruncateThreshold() : int;

    /**
     * @return $this
     */
    public function setTruncateThreshold(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getSearchThreshold() : int;

    /**
     * @return $this
     */
    public function setSearchThreshold(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getSortOrder() : int;

    /**
     * @return $this
     */
    public function setSortOrder(int $value): FacetInterface;

    /**
     * @return bool
     */
    public function getExpandSelection() : bool;

    /**
     * @return $this
     */
    public function setExpandSelection(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsCurrency() : bool;

    /**
     * @return $this
     */
    public function setIsCurrency(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsNumeric() : bool;

    /**
     * @return $this
     */
    public function setIsNumeric(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsSearch() : bool;

    /**
     * @return $this
     */
    public function setIsSearch(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsVisible() : bool;

    /**
     * @return $this
     */
    public function setIsVisible(bool $value): FacetInterface;

    /**
     * @return string
     */
    public function getUBound() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setUBound(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getLBound() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setLBound(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getIncrement() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setIncrement(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getNofVisible() : int;

    /**
     * @return $this
     */
    public function setNofVisible(int $value): FacetInterface;

    /**
     * @return int
     */
    public function getHeight() : int;

    /**
     * @return $this
     */
    public function setHeight(int $value): FacetInterface;

    /**
     * @return string
     */
    public function getDisplayRuleXML() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setDisplayRuleXML(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getSortBy() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSortBy(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getParentId() : int;

    /**
     * @return $this
     */
    public function setParentId(int $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsCollapsible() : bool;

    /**
     * @return $this
     */
    public function setIsCollapsible(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getIsCollapsedDefault() : bool;

    /**
     * @return $this
     */
    public function setIsCollapsedDefault(bool $value): FacetInterface;

    /**
     * @return string
     */
    public function getSwatchData() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSwatchData(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getFacetRangeDisplayType() : int;

    /**
     * @return $this
     */
    public function setFacetRangeDisplayType(int $value): FacetInterface;

    /**
     * @return bool
     */
    public function getPreloadChildren() : bool;

    /**
     * @return $this
     */
    public function setPreloadChildren(bool $value): FacetInterface;

    /**
     * @return string
     */
    public function getTooltip() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTooltip(?string $value): FacetInterface;

    /**
     * @return bool
     */
    public function getShowSliderInputs() : bool;

    /**
     * @return $this
     */
    public function setShowSliderInputs(bool $value): FacetInterface;

    /**
     * @return bool
     */
    public function getShowFacetImageCount() : bool;

    /**
     * @return $this
     */
    public function setShowFacetImageCount(bool $value): FacetInterface;

    /**
     * @return \HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface[]
     */
    public function getFacetRanges() : array;

    /**
     * @param \HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface[]|null $value
     * @return $this
     */
    public function setFacetRanges(?array $value): FacetInterface;

    /**
     * @return string
     */
    public function getTags() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTags(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getCreateDate() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCreateDate(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getModifyDate() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setModifyDate(?string $value): FacetInterface;

    /**
     * @return \HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterface
     */
    public function getBoostBury() : FacetBoostBuryInterface;

    /**
     * @param \HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterface|null $value
     * @return $this
     */
    public function setBoostBury(?FacetBoostBuryInterface $value): FacetInterface;

    /**
     * @return string
     */
    public function getListName() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setListName(?string $value): FacetInterface;

    /**
     * @return int
     */
    public function getNumericPrecision() : int;

    /**
     * @return $this
     */
    public function setNumericPrecision(int $value): FacetInterface;

    /**
     * @return string
     */
    public function getCurrencySymbol() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCurrencySymbol(?string $value): FacetInterface;

    /**
     * @return string
     */
    public function getDefaultItemType() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setDefaultItemType(?string $value): FacetInterface;
}
