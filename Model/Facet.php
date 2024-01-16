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
    /**
     * @var FacetBoostBuryInterfaceFactory
     */
    private FacetBoostBuryInterfaceFactory $facetBoostBuryFactory;

    /**
     * @param FacetBoostBuryInterfaceFactory $facetBoostBuryFactory
     * @param array $data
     */
    public function __construct(
        FacetBoostBuryInterfaceFactory $facetBoostBuryFactory,
        array $data = [
            "Name" => "", // should be specified during facet creation
            "Field" => "",  // should be specified during facet creation
            "FacetType" => "checkbox",
            "FieldType" => "string",
            "MaxCount" => 0,
            "MinHitCount" => 0,
            "DisplayType" => "default",
            "ScrollHeight" => 0,
            "ScrollThreshold" => 0,
            "TruncateThreshold" => 0,
            "SearchThreshold" => 0,
            "SortOrder" => 0,
            "ExpandSelection" => false,
            "IsCurrency" => false,
            "IsNumeric" => false,
            "IsSearch" => false,
            "IsVisible" => false,
            "IsCollapsible" => false,
            "IsCollapsedDefault" => false,
            "ShowSliderInputs" => false
        ]
    ) {
        parent::__construct($data);
        $this->facetBoostBuryFactory = $facetBoostBuryFactory;
    }

    /**
     * @inheritDoc
     */
    public function getFacetId(): int
    {
        return (int)$this->_get(self::FACET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setFacetId(int $value): FacetInterface
    {
        return $this->setData(self::FACET_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSyncGuid(): string
    {
        return (string)$this->_get(self::SYNC_GUID);
    }

    /**
     * @inheritDoc
     */
    public function setSyncGuid(?string $value): FacetInterface
    {
        return $this->setData(self::SYNC_GUID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->_get(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $value): FacetInterface
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetType(): string
    {
        return (string)$this->_get(self::FACET_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setFacetType(?string $value): FacetInterface
    {
        return $this->setData(self::FACET_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFieldType(): string
    {
        return (string)$this->_get(self::FIELD_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setFieldType(?string $value): FacetInterface
    {
        return $this->setData(self::FIELD_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMaxCount(): int
    {
        return (int)$this->_get(self::MAX_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setMaxCount(int $value): FacetInterface
    {
        return $this->setData(self::MAX_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMinHitCount(): int
    {
        return (int)$this->_get(self::MIN_HIT_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setMinHitCount(int $value): FacetInterface
    {
        return $this->setData(self::MIN_HIT_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return (string)$this->_get(self::FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setField(?string $value): FacetInterface
    {
        return $this->setData(self::FIELD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getParam(): string
    {
        return (string)$this->_get(self::PARAM);
    }

    /**
     * @inheritDoc
     */
    public function setParam(?string $value): FacetInterface
    {
        return $this->setData(self::PARAM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDisplayType(): string
    {
        return (string)$this->_get(self::DISPLAY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setDisplayType(?string $value): FacetInterface
    {
        return $this->setData(self::DISPLAY_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getScrollHeight(): int
    {
        return (int)$this->_get(self::SCROLL_HEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setScrollHeight(int $value): FacetInterface
    {
        return $this->setData(self::SCROLL_HEIGHT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getScrollThreshold(): int
    {
        return (int)$this->_get(self::SCROLL_THRESHOLD);
    }

    /**
     * @inheritDoc
     */
    public function setScrollThreshold(int $value): FacetInterface
    {
        return $this->setData(self::SCROLL_THRESHOLD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTruncateThreshold(): int
    {
        return (int)$this->_get(self::TRUNCATE_THRESHOLD);
    }

    /**
     * @inheritDoc
     */
    public function setTruncateThreshold(int $value): FacetInterface
    {
        return $this->setData(self::TRUNCATE_THRESHOLD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSearchThreshold(): int
    {
        return (int)$this->_get(self::SEARCH_THRESHOLD);
    }

    /**
     * @inheritDoc
     */
    public function setSearchThreshold(int $value): FacetInterface
    {
        return $this->setData(self::SEARCH_THRESHOLD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $value): FacetInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExpandSelection(): bool
    {
        return !!$this->_get(self::EXPAND_SELECTION);
    }

    /**
     * @inheritDoc
     */
    public function setExpandSelection(bool $value): FacetInterface
    {
        return $this->setData(self::EXPAND_SELECTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsCurrency(): bool
    {
        return !!$this->_get(self::IS_CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function setIsCurrency(bool $value): FacetInterface
    {
        return $this->setData(self::IS_CURRENCY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsNumeric(): bool
    {
        return !!$this->_get(self::IS_NUMERIC);
    }

    /**
     * @inheritDoc
     */
    public function setIsNumeric(bool $value): FacetInterface
    {
        return $this->setData(self::IS_NUMERIC, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsSearch(): bool
    {
        return !!$this->_get(self::IS_SEARCH);
    }

    /**
     * @inheritDoc
     */
    public function setIsSearch(bool $value): FacetInterface
    {
        return $this->setData(self::IS_SEARCH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsVisible(): bool
    {
        return !!$this->_get(self::IS_VISIBLE);
    }

    /**
     * @inheritDoc
     */
    public function setIsVisible(bool $value): FacetInterface
    {
        return $this->setData(self::IS_VISIBLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUBound(): string
    {
        return (string)$this->_get(self::U_BOUND);
    }

    /**
     * @inheritDoc
     */
    public function setUBound(?string $value): FacetInterface
    {
        return $this->setData(self::U_BOUND, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLBound(): string
    {
        return (string)$this->_get(self::L_BOUND);
    }

    /**
     * @inheritDoc
     */
    public function setLBound(?string $value): FacetInterface
    {
        return $this->setData(self::L_BOUND, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIncrement(): string
    {
        return (string)$this->_get(self::INCREMENT);
    }

    /**
     * @inheritDoc
     */
    public function setIncrement(?string $value): FacetInterface
    {
        return $this->setData(self::INCREMENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getNofVisible(): int
    {
        return (int)$this->_get(self::NOF_VISIBLE);
    }

    /**
     * @inheritDoc
     */
    public function setNofVisible(int $value): FacetInterface
    {
        return $this->setData(self::NOF_VISIBLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHeight(): int
    {
        return (int)$this->_get(self::HEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setHeight(int $value): FacetInterface
    {
        return $this->setData(self::HEIGHT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDisplayRuleXML(): string
    {
        return (string)$this->_get(self::DISPLAY_RULE_XML);
    }

    /**
     * @inheritDoc
     */
    public function setDisplayRuleXML(?string $value): FacetInterface
    {
        return $this->setData(self::DISPLAY_RULE_XML, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortBy(): string
    {
        return (string)$this->_get(self::SORT_BY);
    }

    /**
     * @inheritDoc
     */
    public function setSortBy(?string $value): FacetInterface
    {
        return $this->setData(self::SORT_BY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getParentId(): int
    {
        return (int)$this->_get(self::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentId(int $value): FacetInterface
    {
        return $this->setData(self::PARENT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsCollapsible(): bool
    {
        return !!$this->_get(self::IS_COLLAPSIBLE);
    }

    /**
     * @inheritDoc
     */
    public function setIsCollapsible(bool $value): FacetInterface
    {
        return $this->setData(self::IS_COLLAPSIBLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsCollapsedDefault(): bool
    {
        return !!$this->_get(self::IS_COLLAPSED_DEFAULT);
    }

    /**
     * @inheritDoc
     */
    public function setIsCollapsedDefault(bool $value): FacetInterface
    {
        return $this->setData(self::IS_COLLAPSED_DEFAULT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSwatchData(): string
    {
        return (string)$this->_get(self::SWATCH_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setSwatchData(?string $value): FacetInterface
    {
        return $this->setData(self::SWATCH_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetRangeDisplayType(): int
    {
        return (int)$this->_get(self::FACET_RANGE_DISPLAY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setFacetRangeDisplayType(int $value): FacetInterface
    {
        return $this->setData(self::FACET_RANGE_DISPLAY_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPreloadChildren(): bool
    {
        return !!$this->_get(self::PRELOAD_CHILDREN);
    }

    /**
     * @inheritDoc
     */
    public function setPreloadChildren(bool $value): FacetInterface
    {
        return $this->setData(self::PRELOAD_CHILDREN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTooltip(): string
    {
        return (string)$this->_get(self::TOOLTIP);
    }

    /**
     * @inheritDoc
     */
    public function setTooltip(?string $value): FacetInterface
    {
        return $this->setData(self::TOOLTIP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShowSliderInputs(): bool
    {
        return !!$this->_get(self::SHOW_SLIDER_INPUTS);
    }

    /**
     * @inheritDoc
     */
    public function setShowSliderInputs(bool $value): FacetInterface
    {
        return $this->setData(self::SHOW_SLIDER_INPUTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShowFacetImageCount(): bool
    {
        return !!$this->_get(self::SHOW_FACET_IMAGE_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShowFacetImageCount(bool $value): FacetInterface
    {
        return $this->setData(self::SHOW_FACET_IMAGE_COUNT, $value);
    }

    /**
     * @inheritDoc
     * @throws \InvalidArgumentException
     */
    public function getFacetRanges(): array
    {
        $value = $this->_get(self::FACET_RANGES) ?? [];
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            FacetRangeModelInterface::class
        );

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setFacetRanges(?array $value): FacetInterface
    {
        return $this->setData(self::FACET_RANGES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTags(): string
    {
        return (string)$this->_get(self::TAGS);
    }

    /**
     * @inheritDoc
     */
    public function setTags(?string $value): FacetInterface
    {
        return $this->setData(self::TAGS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate(): string
    {
        return (string)$this->_get(self::CREATE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate(?string $value): FacetInterface
    {
        return $this->setData(self::CREATE_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getModifyDate(): string
    {
        return (string)$this->_get(self::MODIFY_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setModifyDate(?string $value): FacetInterface
    {
        return $this->setData(self::MODIFY_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBoostBury(): FacetBoostBuryInterface
    {
        return $this->_get(self::BOOST_BURY) ?? $this->facetBoostBuryFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function setBoostBury(?FacetBoostBuryInterface $value): FacetInterface
    {
        return $this->setData(self::BOOST_BURY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getListName(): string
    {
        return (string)$this->_get(self::LIST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setListName(?string $value): FacetInterface
    {
        return $this->setData(self::LIST_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getNumericPrecision(): int
    {
        return (int)$this->_get(self::NUMERIC_PRECISION);
    }

    /**
     * @inheritDoc
     */
    public function setNumericPrecision(int $value): FacetInterface
    {
        return $this->setData(self::NUMERIC_PRECISION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCurrencySymbol(): string
    {
        return (string)$this->_get(self::CURRENCY_SYMBOL);
    }

    /**
     * @inheritDoc
     */
    public function setCurrencySymbol(?string $value): FacetInterface
    {
        return $this->setData(self::CURRENCY_SYMBOL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultItemType(): string
    {
        return (string)$this->_get(self::DEFAULT_ITEM_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultItemType(?string $value): FacetInterface
    {
        return $this->setData(self::DEFAULT_ITEM_TYPE, $value);
    }
}
