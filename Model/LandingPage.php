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

    public function setPageId(?int $value)
    {
        return $this->setData(self::FIELD_PAGE_ID, $value);
    }

    public function getSyncGuid(): ?string
    {
        return $this->_get(self::FIELD_SYNC_GUID);
    }

    public function setSyncGuid(?string $value)
    {
        return $this->setData(self::FIELD_SYNC_GUID, $value);
    }

    public function getName(): ?string
    {
        return $this->_get(self::FIELD_NAME);
    }

    public function setName(?string $value)
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    public function getCustomUrl(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_URL);
    }

    public function setCustomUrl(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
    }

    public function getNarrowXml(): ?string
    {
        return $this->_get(self::FIELD_NARROW_XML);
    }

    public function setNarrowXml(?string $value)
    {
        return $this->setData(self::FIELD_NARROW_XML, $value);
    }

    public function getIsFacetOverride(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_FACET_OVERRIDE);
    }

    public function setIsFacetOverride(bool $value)
    {
        return $this->setData(self::FIELD_IS_FACET_OVERRIDE, $value);
    }

    public function getIsIncludeProducts(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_INCLUDE_PRODUCTS);
    }

    public function setIsIncludeProducts(bool $value)
    {
        return $this->setData(self::FIELD_IS_INCLUDE_PRODUCTS, $value);
    }

    public function getSortFieldId(): ?string
    {
        return $this->_get(self::FIELD_SORT_FIELD_ID);
    }

    public function setSortFieldId(?string $value)
    {
        return $this->setData(self::FIELD_SORT_FIELD_ID, $value);
    }

    public function getSortDirection(): ?string
    {
        return $this->_get(self::FIELD_SORT_DIRECTION);
    }

    public function setSortDirection(?string $value)
    {
        return $this->setData(self::FIELD_SORT_DIRECTION, $value);
    }

    public function getSelectedFacets(): array
    {
        return (array)$this->_get(self::FIELD_SELECTED_FACETS);
    }

    public function setSelectedFacets(array $value)
    {
        return $this->setData(self::FIELD_SELECTED_FACETS, $value);
    }

    public function getPageLayoutId(): ?string
    {
        return $this->_get(self::FIELD_PAGE_LAYOUT_ID);
    }

    public function setPageLayoutId(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_LAYOUT_ID, $value);
    }

    public function getEnableFacetAutoOrdering(): bool
    {
        return (bool)$this->_get(self::FIELD_ENABLE_FACET_AUTO_ORDERING);
    }

    public function setEnableFacetAutoOrdering(bool $value)
    {
        return $this->setData(self::FIELD_ENABLE_FACET_AUTO_ORDERING, $value);
    }

    public function getCustom(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM);
    }

    public function setCustom(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM, $value);
    }

    public function getTags(): ?string
    {
        return $this->_get(self::FIELD_TAGS);
    }

    public function setTags(?string $value)
    {
        return $this->setData(self::FIELD_TAGS, $value);
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->_get(self::FIELD_CANONICAL_URL);
    }

    public function setCanonicalUrl(?string $value)
    {
        return $this->setData(self::FIELD_CANONICAL_URL, $value);
    }

    public function getPageType(): ?string
    {
        return $this->_get(self::FIELD_PAGE_TYPE);
    }

    public function setPageType(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_TYPE, $value);
    }

    public function getContentConfigList(): array
    {
        return (array)$this->_get(self::FIELD_CONTENT_CONFIG_LIST);
    }

    public function setContentConfigList(array $value)
    {
        return $this->setData(self::FIELD_CONTENT_CONFIG_LIST, $value);
    }

    public function getPageHeading(): ?string
    {
        return $this->_get(self::FIELD_PAGE_HEADING);
    }

    public function setPageHeading(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_HEADING, $value);
    }

    public function getCustomHtml(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_HTML);
    }

    public function setCustomHtml(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_HTML, $value);
    }

    public function getKeywords(): ?string
    {
        return $this->_get(self::FIELD_KEYWORDS);
    }

    public function setKeywords(?string $value)
    {
        return $this->setData(self::FIELD_KEYWORDS, $value);
    }

    public function getListName(): ?string
    {
        return $this->_get(self::FIELD_LIST_NAME);
    }

    public function setListName(?string $value)
    {
        return $this->setData(self::FIELD_LIST_NAME, $value);
    }

    public function getNotes(): ?string
    {
        return $this->_get(self::FIELD_NOTES);
    }

    public function setNotes(?string $value)
    {
        return $this->setData(self::FIELD_NOTES, $value);
    }

    public function getCreateDate(): ?string
    {
        return $this->_get(self::FIELD_CREATE_DATE);
    }

    public function setCreateDate(?string $value)
    {
        return $this->setData(self::FIELD_CREATE_DATE, $value);
    }

    public function getModifyDate(): ?string
    {
        return $this->_get(self::FIELD_MODIFY_DATE);
    }

    public function setModifyDate(?string $value)
    {
        return $this->setData(self::FIELD_MODIFY_DATE, $value);
    }

    public function getIsNoIndex(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_INDEX);
    }

    public function setIsNoIndex(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_INDEX, $value);
    }

    public function getIsNoFollow(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_FOLLOW);
    }

    public function setIsNoFollow(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_FOLLOW, $value);
    }

    public function getCustomSortList(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_SORT_LIST);
    }

    public function setCustomSortList(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_SORT_LIST, $value);
    }
}
