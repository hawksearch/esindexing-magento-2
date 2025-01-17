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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterface;
use HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use Magento\Framework\Api\AbstractSimpleObject;

class Facet extends AbstractSimpleObject implements FacetInterface
{
    private FacetBoostBuryInterfaceFactory $facetBoostBuryFactory;

    /**
     * @param FacetBoostBuryInterfaceFactory $facetBoostBuryFactory
     * @param array<self::*, mixed> $data
     */
    public function __construct(
        FacetBoostBuryInterfaceFactory $facetBoostBuryFactory,
        array $data = [
            self::NAME => "", // should be specified during facet creation
            self::FIELD => "",  // should be specified during facet creation
            self::FACET_TYPE => "checkbox",
            self::FIELD_TYPE => "string",
            self::MAX_COUNT => 0,
            self::MIN_HIT_COUNT => 0,
            self::DISPLAY_TYPE => "default",
            self::SCROLL_HEIGHT => 0,
            self::SCROLL_THRESHOLD => 0,
            self::TRUNCATE_THRESHOLD => 0,
            self::SEARCH_THRESHOLD => 0,
            self::SORT_ORDER => 0,
            self::EXPAND_SELECTION => false,
            self::IS_CURRENCY => false,
            self::IS_NUMERIC => false,
            self::IS_SEARCH => false,
            self::IS_VISIBLE => false,
            self::IS_COLLAPSIBLE => false,
            self::IS_COLLAPSED_DEFAULT => false,
            self::SHOW_SLIDER_INPUTS => false
        ]
    )
    {
        parent::__construct($data);
        $this->facetBoostBuryFactory = $facetBoostBuryFactory;
    }

    public function getSyncGuid(): string
    {
        return (string)$this->_get(self::SYNC_GUID);
    }

    public function setSyncGuid(?string $value): FacetInterface
    {
        return $this->setData(self::SYNC_GUID, $value);
    }

    public function getFacetId(): int
    {
        return (int)$this->_get(self::FACET_ID);
    }

    public function setFacetId(int $value): FacetInterface
    {
        return $this->setData(self::FACET_ID, $value);
    }

    public function getName(): string
    {
        return (string)$this->_get(self::NAME);
    }

    public function setName(?string $value): FacetInterface
    {
        return $this->setData(self::NAME, $value);
    }

    public function getFacetType(): string
    {
        return (string)$this->_get(self::FACET_TYPE);
    }

    public function setFacetType(?string $value): FacetInterface
    {
        return $this->setData(self::FACET_TYPE, $value);
    }

    public function getFieldType(): string
    {
        return (string)$this->_get(self::FIELD_TYPE);
    }

    public function setFieldType(?string $value): FacetInterface
    {
        return $this->setData(self::FIELD_TYPE, $value);
    }

    public function getMaxCount(): int
    {
        return (int)$this->_get(self::MAX_COUNT);
    }

    public function setMaxCount(int $value): FacetInterface
    {
        return $this->setData(self::MAX_COUNT, $value);
    }

    public function getMinHitCount(): int
    {
        return (int)$this->_get(self::MIN_HIT_COUNT);
    }

    public function setMinHitCount(int $value): FacetInterface
    {
        return $this->setData(self::MIN_HIT_COUNT, $value);
    }

    public function getField(): string
    {
        return (string)$this->_get(self::FIELD);
    }

    public function setField(?string $value): FacetInterface
    {
        return $this->setData(self::FIELD, $value);
    }

    public function getParam(): string
    {
        return (string)$this->_get(self::PARAM);
    }

    public function setParam(?string $value): FacetInterface
    {
        return $this->setData(self::PARAM, $value);
    }

    public function getDisplayType(): string
    {
        return (string)$this->_get(self::DISPLAY_TYPE);
    }

    public function setDisplayType(?string $value): FacetInterface
    {
        return $this->setData(self::DISPLAY_TYPE, $value);
    }

    public function getScrollHeight(): int
    {
        return (int)$this->_get(self::SCROLL_HEIGHT);
    }

    public function setScrollHeight(int $value): FacetInterface
    {
        return $this->setData(self::SCROLL_HEIGHT, $value);
    }

    public function getScrollThreshold(): int
    {
        return (int)$this->_get(self::SCROLL_THRESHOLD);
    }

    public function setScrollThreshold(int $value): FacetInterface
    {
        return $this->setData(self::SCROLL_THRESHOLD, $value);
    }

    public function getTruncateThreshold(): int
    {
        return (int)$this->_get(self::TRUNCATE_THRESHOLD);
    }

    public function setTruncateThreshold(int $value): FacetInterface
    {
        return $this->setData(self::TRUNCATE_THRESHOLD, $value);
    }

    public function getSearchThreshold(): int
    {
        return (int)$this->_get(self::SEARCH_THRESHOLD);
    }

    public function setSearchThreshold(int $value): FacetInterface
    {
        return $this->setData(self::SEARCH_THRESHOLD, $value);
    }

    public function getSortOrder(): int
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    public function setSortOrder(int $value): FacetInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    public function getExpandSelection(): bool
    {
        return !!$this->_get(self::EXPAND_SELECTION);
    }

    public function setExpandSelection(bool $value): FacetInterface
    {
        return $this->setData(self::EXPAND_SELECTION, $value);
    }

    public function getIsCurrency(): bool
    {
        return !!$this->_get(self::IS_CURRENCY);
    }

    public function setIsCurrency(bool $value): FacetInterface
    {
        return $this->setData(self::IS_CURRENCY, $value);
    }

    public function getIsNumeric(): bool
    {
        return !!$this->_get(self::IS_NUMERIC);
    }

    public function setIsNumeric(bool $value): FacetInterface
    {
        return $this->setData(self::IS_NUMERIC, $value);
    }

    public function getIsSearch(): bool
    {
        return !!$this->_get(self::IS_SEARCH);
    }

    public function setIsSearch(bool $value): FacetInterface
    {
        return $this->setData(self::IS_SEARCH, $value);
    }

    public function getIsVisible(): bool
    {
        return !!$this->_get(self::IS_VISIBLE);
    }

    public function setIsVisible(bool $value): FacetInterface
    {
        return $this->setData(self::IS_VISIBLE, $value);
    }

    public function getUBound(): string
    {
        return (string)$this->_get(self::U_BOUND);
    }

    public function setUBound(?string $value): FacetInterface
    {
        return $this->setData(self::U_BOUND, $value);
    }

    public function getLBound(): string
    {
        return (string)$this->_get(self::L_BOUND);
    }

    public function setLBound(?string $value): FacetInterface
    {
        return $this->setData(self::L_BOUND, $value);
    }

    public function getIncrement(): string
    {
        return (string)$this->_get(self::INCREMENT);
    }

    public function setIncrement(?string $value): FacetInterface
    {
        return $this->setData(self::INCREMENT, $value);
    }

    public function getNofVisible(): int
    {
        return (int)$this->_get(self::NOF_VISIBLE);
    }

    public function setNofVisible(int $value): FacetInterface
    {
        return $this->setData(self::NOF_VISIBLE, $value);
    }

    public function getHeight(): int
    {
        return (int)$this->_get(self::HEIGHT);
    }

    public function setHeight(int $value): FacetInterface
    {
        return $this->setData(self::HEIGHT, $value);
    }

    public function getDisplayRuleXML(): string
    {
        return (string)$this->_get(self::DISPLAY_RULE_XML);
    }

    public function setDisplayRuleXML(?string $value): FacetInterface
    {
        return $this->setData(self::DISPLAY_RULE_XML, $value);
    }

    public function getSortBy(): string
    {
        return (string)$this->_get(self::SORT_BY);
    }

    public function setSortBy(?string $value): FacetInterface
    {
        return $this->setData(self::SORT_BY, $value);
    }

    public function getParentId(): int
    {
        return (int)$this->_get(self::PARENT_ID);
    }

    public function setParentId(int $value): FacetInterface
    {
        return $this->setData(self::PARENT_ID, $value);
    }

    public function getIsCollapsible(): bool
    {
        return !!$this->_get(self::IS_COLLAPSIBLE);
    }

    public function setIsCollapsible(bool $value): FacetInterface
    {
        return $this->setData(self::IS_COLLAPSIBLE, $value);
    }

    public function getIsCollapsedDefault(): bool
    {
        return !!$this->_get(self::IS_COLLAPSED_DEFAULT);
    }

    public function setIsCollapsedDefault(bool $value): FacetInterface
    {
        return $this->setData(self::IS_COLLAPSED_DEFAULT, $value);
    }

    public function getSwatchData(): string
    {
        return (string)$this->_get(self::SWATCH_DATA);
    }

    public function setSwatchData(?string $value): FacetInterface
    {
        return $this->setData(self::SWATCH_DATA, $value);
    }

    public function getFacetRangeDisplayType(): int
    {
        return (int)$this->_get(self::FACET_RANGE_DISPLAY_TYPE);
    }

    public function setFacetRangeDisplayType(int $value): FacetInterface
    {
        return $this->setData(self::FACET_RANGE_DISPLAY_TYPE, $value);
    }

    public function getPreloadChildren(): bool
    {
        return !!$this->_get(self::PRELOAD_CHILDREN);
    }

    public function setPreloadChildren(bool $value): FacetInterface
    {
        return $this->setData(self::PRELOAD_CHILDREN, $value);
    }

    public function getTooltip(): string
    {
        return (string)$this->_get(self::TOOLTIP);
    }

    public function setTooltip(?string $value): FacetInterface
    {
        return $this->setData(self::TOOLTIP, $value);
    }

    public function getShowSliderInputs(): bool
    {
        return !!$this->_get(self::SHOW_SLIDER_INPUTS);
    }

    public function setShowSliderInputs(bool $value): FacetInterface
    {
        return $this->setData(self::SHOW_SLIDER_INPUTS, $value);
    }

    public function getShowFacetImageCount(): bool
    {
        return !!$this->_get(self::SHOW_FACET_IMAGE_COUNT);
    }

    public function setShowFacetImageCount(bool $value): FacetInterface
    {
        return $this->setData(self::SHOW_FACET_IMAGE_COUNT, $value);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getFacetRanges(): array
    {
        $value = (array)($this->_get(self::FACET_RANGES) ?? []);
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            FacetRangeModelInterface::class
        );

        return $value;
    }

    public function setFacetRanges(?array $value): FacetInterface
    {
        return $this->setData(self::FACET_RANGES, $value);
    }

    public function getTags(): string
    {
        return (string)$this->_get(self::TAGS);
    }

    public function setTags(?string $value): FacetInterface
    {
        return $this->setData(self::TAGS, $value);
    }

    public function getCreateDate(): string
    {
        return (string)$this->_get(self::CREATE_DATE);
    }

    public function setCreateDate(?string $value): FacetInterface
    {
        return $this->setData(self::CREATE_DATE, $value);
    }

    public function getModifyDate(): string
    {
        return (string)$this->_get(self::MODIFY_DATE);
    }

    public function setModifyDate(?string $value): FacetInterface
    {
        return $this->setData(self::MODIFY_DATE, $value);
    }

    public function getBoostBury(): FacetBoostBuryInterface
    {
        return $this->_get(self::BOOST_BURY) ?? $this->facetBoostBuryFactory->create();
    }

    public function setBoostBury(?FacetBoostBuryInterface $value): FacetInterface
    {
        return $this->setData(self::BOOST_BURY, $value);
    }

    public function getListName(): string
    {
        return (string)$this->_get(self::LIST_NAME);
    }

    public function setListName(?string $value): FacetInterface
    {
        return $this->setData(self::LIST_NAME, $value);
    }

    public function getNumericPrecision(): int
    {
        return (int)$this->_get(self::NUMERIC_PRECISION);
    }

    public function setNumericPrecision(int $value): FacetInterface
    {
        return $this->setData(self::NUMERIC_PRECISION, $value);
    }

    public function getCurrencySymbol(): string
    {
        return (string)$this->_get(self::CURRENCY_SYMBOL);
    }

    public function setCurrencySymbol(?string $value): FacetInterface
    {
        return $this->setData(self::CURRENCY_SYMBOL, $value);
    }

    public function getDefaultItemType(): string
    {
        return (string)$this->_get(self::DEFAULT_ITEM_TYPE);
    }

    public function setDefaultItemType(?string $value): FacetInterface
    {
        return $this->setData(self::DEFAULT_ITEM_TYPE, $value);
    }
}
