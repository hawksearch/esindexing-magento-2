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

    /**
     * @inheritDoc
     */
    public function getPageId(): ?int
    {
        return (int)$this->_get(self::FIELD_PAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPageId(?int $value)
    {
        return $this->setData(self::FIELD_PAGE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSyncGuid(): ?string
    {
        return $this->_get(self::FIELD_SYNC_GUID);
    }

    /**
     * @inheritDoc
     */
    public function setSyncGuid(?string $value)
    {
        return $this->setData(self::FIELD_SYNC_GUID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->_get(self::FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $value)
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomUrl(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_URL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomUrl(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getNarrowXml(): ?string
    {
        return $this->_get(self::FIELD_NARROW_XML);
    }

    /**
     * @inheritDoc
     */
    public function setNarrowXml(?string $value)
    {
        return $this->setData(self::FIELD_NARROW_XML, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsFacetOverride(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_FACET_OVERRIDE);
    }

    /**
     * @inheritDoc
     */
    public function setIsFacetOverride(bool $value)
    {
        return $this->setData(self::FIELD_IS_FACET_OVERRIDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsIncludeProducts(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_INCLUDE_PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setIsIncludeProducts(bool $value)
    {
        return $this->setData(self::FIELD_IS_INCLUDE_PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortFieldId(): ?string
    {
        return $this->_get(self::FIELD_SORT_FIELD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSortFieldId(?string $value)
    {
        return $this->setData(self::FIELD_SORT_FIELD_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortDirection(): ?string
    {
        return $this->_get(self::FIELD_SORT_DIRECTION);
    }

    /**
     * @inheritDoc
     */
    public function setSortDirection(?string $value)
    {
        return $this->setData(self::FIELD_SORT_DIRECTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSelectedFacets(): array
    {
        return (array)$this->_get(self::FIELD_SELECTED_FACETS);
    }

    /**
     * @inheritDoc
     */
    public function setSelectedFacets(array $value)
    {
        return $this->setData(self::FIELD_SELECTED_FACETS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPageLayoutId(): ?string
    {
        return $this->_get(self::FIELD_PAGE_LAYOUT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPageLayoutId(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_LAYOUT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getEnableFacetAutoOrdering(): bool
    {
        return (bool)$this->_get(self::FIELD_ENABLE_FACET_AUTO_ORDERING);
    }

    /**
     * @inheritDoc
     */
    public function setEnableFacetAutoOrdering(bool $value)
    {
        return $this->setData(self::FIELD_ENABLE_FACET_AUTO_ORDERING, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustom(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM);
    }

    /**
     * @inheritDoc
     */
    public function setCustom(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTags(): ?string
    {
        return $this->_get(self::FIELD_TAGS);
    }

    /**
     * @inheritDoc
     */
    public function setTags(?string $value)
    {
        return $this->setData(self::FIELD_TAGS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCanonicalUrl(): ?string
    {
        return $this->_get(self::FIELD_CANONICAL_URL);
    }

    /**
     * @inheritDoc
     */
    public function setCanonicalUrl(?string $value)
    {
        return $this->setData(self::FIELD_CANONICAL_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPageType(): ?string
    {
        return $this->_get(self::FIELD_PAGE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setPageType(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getContentConfigList(): array
    {
        return (array)$this->_get(self::FIELD_CONTENT_CONFIG_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setContentConfigList(array $value)
    {
        return $this->setData(self::FIELD_CONTENT_CONFIG_LIST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPageHeading(): ?string
    {
        return $this->_get(self::FIELD_PAGE_HEADING);
    }

    /**
     * @inheritDoc
     */
    public function setPageHeading(?string $value)
    {
        return $this->setData(self::FIELD_PAGE_HEADING, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomHtml(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_HTML);
    }

    /**
     * @inheritDoc
     */
    public function setCustomHtml(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_HTML, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKeywords(): ?string
    {
        return $this->_get(self::FIELD_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function setKeywords(?string $value)
    {
        return $this->setData(self::FIELD_KEYWORDS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getListName(): ?string
    {
        return $this->_get(self::FIELD_LIST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setListName(?string $value)
    {
        return $this->setData(self::FIELD_LIST_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getNotes(): ?string
    {
        return $this->_get(self::FIELD_NOTES);
    }

    /**
     * @inheritDoc
     */
    public function setNotes(?string $value)
    {
        return $this->setData(self::FIELD_NOTES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate(): ?string
    {
        return $this->_get(self::FIELD_CREATE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate(?string $value)
    {
        return $this->setData(self::FIELD_CREATE_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getModifyDate(): ?string
    {
        return $this->_get(self::FIELD_MODIFY_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setModifyDate(?string $value)
    {
        return $this->setData(self::FIELD_MODIFY_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsNoIndex(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_INDEX);
    }

    /**
     * @inheritDoc
     */
    public function setIsNoIndex(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_INDEX, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsNoFollow(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_NO_FOLLOW);
    }

    /**
     * @inheritDoc
     */
    public function setIsNoFollow(bool $value)
    {
        return $this->setData(self::FIELD_IS_NO_FOLLOW, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomSortList(): ?string
    {
        return $this->_get(self::FIELD_CUSTOM_SORT_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setCustomSortList(?string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_SORT_LIST, $value);
    }
}
