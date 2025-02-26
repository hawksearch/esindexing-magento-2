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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class LandingPage extends AbstractSimpleObject implements LandingPageInterface
{
    public function getPageId(): ?int
    {
        return (int)$this->_get(self::FIELD_PAGE_ID);
    }

    /**
     * @return $this
     */
    public function setPageId(?int $value)
    {
        return $this->setData(self::FIELD_PAGE_ID, $value);
    }

    public function getSyncGuid(): ?string
    {
        return $this->_get(self::FIELD_SYNC_GUID);
    }

    /**
     * @return $this
     */
    public function setSyncGuid(?string $value)
    {
        return $this->setData(self::FIELD_SYNC_GUID, $value);
    }

    public function getName(): ?string
    {
        return $this->_get(self::FIELD_NAME);
    }

    /**
     * @return $this
     */
    public function setName(?string $value)
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    public function getCustomUrl(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_URL);
    }

    /**
     * @return $this
     */
    public function setCustomUrl(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
    }

    public function getNarrowXml(): ?string
    {
        return $this->_get(self::FIELD_NARROW_XML);
    }

    /**
     * @return $this
     */
    public function setNarrowXml(?string $value)
    {
        return $this->setData(self::FIELD_NARROW_XML, $value);
    }

    public function getIsFacetOverride(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_FACET_OVERRIDE);
    }

    /**
     * @return $this
     */
    public function setIsFacetOverride(bool $value)
    {
        return $this->setData(self::FIELD_IS_FACET_OVERRIDE, $value);
    }

    public function getIsIncludeProducts(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_INCLUDE_PRODUCTS);
    }

    /**
     * @return $this
     */
    public function setIsIncludeProducts(bool $value)
    {
        return $this->setData(self::FIELD_IS_INCLUDE_PRODUCTS, $value);
    }

    public function getSortFieldId(): ?string
    {
        return $this->_get(self::FIELD_SORT_FIELD_ID);
    }

    /**
     * @return $this
     */
    public function setSortFieldId(?string $value)
    {
        return $this->setData(self::FIELD_SORT_FIELD_ID, $value);
    }

    public function getSortDirection(): ?string
    {
        return $this->_get(self::FIELD_SORT_DIRECTION);
    }

    /**
     * @return $this
     */
    public function setSortDirection(?string $value)
    {
        return $this->setData(self::FIELD_SORT_DIRECTION, $value);
    }

    public function getSelectedFacets(): array
    {
        return (array)$this->_get(self::FIELD_SELECTED_FACETS);
    }

    /**
     * @return $this
     */
    public function setSelectedFacets(array $value)
    {
        return $this->setData(self::FIELD_SELECTED_FACETS, $value);
    }

    public function getPageLayoutId(): ?string
    {
        return $this->_get(self::FIELD_PAGE_LAYOUT_ID);
    }

    /**
     * @return $this
     */
    public function setPageLayoutId(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_LAYOUT_ID, $value);
    }

    public function getEnableFacetAutoOrdering(): bool
    {
        return (bool)$this->_get(self::FIELD_ENABLE_FACET_AUTO_ORDERING);
    }

    /**
     * @return $this
     */
    public function setEnableFacetAutoOrdering(bool $value)
    {
        return $this->setData(self::FIELD_ENABLE_FACET_AUTO_ORDERING, $value);
    }

    public function getCustom(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM);
    }

    /**
     * @return $this
     */
    public function setCustom(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM, $value);
    }

    public function getTags(): ?string
    {
        return $this->_get(self::FIELD_TAGS);
    }

    /**
     * @return $this
     */
    public function setTags(?string $value)
    {
        return $this->setData(self::FIELD_TAGS, $value);
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->_get(self::FIELD_CANONICAL_URL);
    }

    /**
     * @return $this
     */
    public function setCanonicalUrl(?string $value)
    {
        return $this->setData(self::FIELD_CANONICAL_URL, $value);
    }

    public function getPageType(): ?string
    {
        return $this->_get(self::FIELD_PAGE_TYPE);
    }

    /**
     * @return $this
     */
    public function setPageType(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_TYPE, $value);
    }

    public function getContentConfigList(): array
    {
        return (array)$this->_get(self::FIELD_CONTENT_CONFIG_LIST);
    }

    /**
     * @return $this
     */
    public function setContentConfigList(array $value)
    {
        return $this->setData(self::FIELD_CONTENT_CONFIG_LIST, $value);
    }

    public function getPageHeading(): ?string
    {
        return $this->_get(self::FIELD_PAGE_HEADING);
    }

    /**
     * @return $this
     */
    public function setPageHeading(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_HEADING, $value);
    }

    public function getCustomHtml(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_HTML);
    }

    /**
     * @return $this
     */
    public function setCustomHtml(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_HTML, $value);
    }

    public function getKeywords(): ?string
    {
        return $this->_get(self::FIELD_KEYWORDS);
    }

    /**
     * @return $this
     */
    public function setKeywords(?string $value)
    {
        return $this->setData(self::FIELD_KEYWORDS, $value);
    }

    public function getListName(): ?string
    {
        return $this->_get(self::FIELD_LIST_NAME);
    }

    /**
     * @return $this
     */
    public function setListName(?string $value)
    {
        return $this->setData(self::FIELD_LIST_NAME, $value);
    }

    public function getNotes(): ?string
    {
        return $this->_get(self::FIELD_NOTES);
    }

    /**
     * @return $this
     */
    public function setNotes(?string $value)
    {
        return $this->setData(self::FIELD_NOTES, $value);
    }

    public function getCreateDate(): ?string
    {
        return $this->_get(self::FIELD_CREATE_DATE);
    }

    /**
     * @return $this
     */
    public function setCreateDate(?string $value)
    {
        return $this->setData(self::FIELD_CREATE_DATE, $value);
    }

    public function getModifyDate(): ?string
    {
        return $this->_get(self::FIELD_MODIFY_DATE);
    }

    /**
     * @return $this
     */
    public function setModifyDate(?string $value)
    {
        return $this->setData(self::FIELD_MODIFY_DATE, $value);
    }

    public function getIsNoIndex(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_INDEX);
    }

    /**
     * @return $this
     */
    public function setIsNoIndex(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_INDEX, $value);
    }

    public function getIsNoFollow(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_FOLLOW);
    }

    /**
     * @return $this
     */
    public function setIsNoFollow(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_FOLLOW, $value);
    }

    public function getCustomSortList(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_SORT_LIST);
    }

    /**
     * @return $this
     */
    public function setCustomSortList(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_SORT_LIST, $value);
    }
}
