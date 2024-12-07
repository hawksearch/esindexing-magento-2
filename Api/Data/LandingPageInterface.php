<?php
/**
 * Copyright (c) 2023 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
namespace HawkSearch\EsIndexing\Api\Data;

/**
 * LandingPage Interface used in HawkSearch API
 *
 * @api v11
 * @since 0.8.0
 * @link https://developerdocs.hawksearch.com/reference/landingpage_postvalue_value
 * @link https://dev.hawksearch.net/swagger/ui/index#!/LandingPage/LandingPage_PostValue_value
 */
interface LandingPageInterface
{
    /**#@+
     * Constants for keys of data array
     */
    public const FIELD_PAGE_ID = "PageId";
    public const FIELD_SYNC_GUID = "SyncGuid";
    public const FIELD_NAME = "Name";
    public const FIELD_CUSTOM_URL = "CustomUrl";
    public const FIELD_NARROW_XML = "NarrowXml";
    public const FIELD_IS_FACET_OVERRIDE = "IsFacetOverride";
    public const FIELD_IS_INCLUDE_PRODUCTS = "IsIncludeProducts";
    public const FIELD_SORT_FIELD_ID = "SortFieldId";
    public const FIELD_SORT_DIRECTION = "SortDirection";
    public const FIELD_SELECTED_FACETS = "SelectedFacets";
    public const FIELD_PAGE_LAYOUT_ID = "PageLayoutId";
    public const FIELD_ENABLE_FACET_AUTO_ORDERING = "EnableFacetAutoOrdering";
    public const FIELD_CUSTOM = "Custom";
    public const FIELD_TAGS = "Tags";
    public const FIELD_CANONICAL_URL = "CanonicalUrl";
    public const FIELD_PAGE_TYPE = "PageType";
    public const FIELD_CONTENT_CONFIG_LIST = "ContentConfigList";
    public const FIELD_PAGE_HEADING = "PageHeading";
    public const FIELD_CUSTOM_HTML = "CustomHtml";
    public const FIELD_KEYWORDS = "Keywords";
    public const FIELD_LIST_NAME = "ListName";
    public const FIELD_NOTES = "Notes";
    public const FIELD_CREATE_DATE = "CreateDate";
    public const FIELD_MODIFY_DATE = "ModifyDate";
    public const FIELD_IS_NO_INDEX = "IsNoIndex";
    public const FIELD_IS_NO_FOLLOW = "IsNoFollow";
    public const FIELD_CUSTOM_SORT_LIST = "CustomSortList";
    /**#@-*/

    /**
     * @return int|null
     */
    public function getPageId(): ?int;

    /**
     * @param int|null $value
     * @return $this
     */
    public function setPageId(?int $value);

    /**
     * @return string|null
     */
    public function getSyncGuid(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSyncGuid(?string $value);

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setName(?string $value);

    /**
     * @return string|null
     */
    public function getCustomUrl(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCustomUrl(?string $value);

    /**
     * @return string|null
     */
    public function getNarrowXml(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setNarrowXml(?string $value);

    /**
     * @return bool
     */
    public function getIsFacetOverride(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsFacetOverride(bool $value);

    /**
     * @return bool
     */
    public function getIsIncludeProducts(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsIncludeProducts(bool $value);

    /**
     * @return string|null
     */
    public function getSortFieldId(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSortFieldId(?string $value);

    /**
     * @return string|null
     */
    public function getSortDirection(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSortDirection(?string $value);

    /**
     * @return string[]
     */
    public function getSelectedFacets(): array;

    /**
     * @param string[] $value
     * @return $this
     */
    public function setSelectedFacets(array $value);

    /**
     * @return string|null
     */
    public function getPageLayoutId(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPageLayoutId(?string $value);

    /**
     * @return bool
     */
    public function getEnableFacetAutoOrdering(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setEnableFacetAutoOrdering(bool $value);

    /**
     * @return string|null
     */
    public function getCustom(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCustom(?string $value);

    /**
     * @return string|null
     */
    public function getTags(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTags(?string $value);

    /**
     * @return string|null
     */
    public function getCanonicalUrl(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCanonicalUrl(?string $value);

    /**
     * @return string|null
     */
    public function getPageType(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPageType(?string $value);

    /**
     * @return string[]
     */
    public function getContentConfigList(): array;

    /**
     * @param string[] $value
     * @return $this
     */
    public function setContentConfigList(array $value);

    /**
     * @return string|null
     */
    public function getPageHeading(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPageHeading(?string $value);

    /**
     * @return string|null
     */
    public function getCustomHtml(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCustomHtml(?string $value);

    /**
     * @return string|null
     */
    public function getKeywords(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setKeywords(?string $value);

    /**
     * @return string|null
     */
    public function getListName(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setListName(?string $value);

    /**
     * @return string|null
     */
    public function getNotes(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setNotes(?string $value);

    /**
     * @return string|null
     */
    public function getCreateDate(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCreateDate(?string $value);

    /**
     * @return string|null
     */
    public function getModifyDate(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setModifyDate(?string $value);

    /**
     * @return bool
     */
    public function getIsNoIndex(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsNoIndex(bool $value);

    /**
     * @return bool
     */
    public function getIsNoFollow(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsNoFollow(bool $value);

    /**
     * @return string|null
     */
    public function getCustomSortList(): ?string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCustomSortList(?string $value);

}
