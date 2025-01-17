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

use HawkSearch\EsIndexing\Api\Data\BoostQueryInterface;
use HawkSearch\EsIndexing\Api\Data\ClientDataInterface;
use HawkSearch\EsIndexing\Api\Data\ClientDataInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\SearchRequestInterface;
use HawkSearch\EsIndexing\Api\Data\SmartBarInterface;
use HawkSearch\EsIndexing\Api\Data\SmartBarInterfaceFactory;
use HawkSearch\EsIndexing\Api\Data\VariantOptionsInterface;
use HawkSearch\EsIndexing\Api\Data\VariantOptionsInterfaceFactory;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use Magento\Framework\Api\AbstractSimpleObject;

class SearchRequest extends AbstractSimpleObject implements SearchRequestInterface
{
    private ClientDataInterfaceFactory $clientDataFactory;
    private VariantOptionsInterfaceFactory $variantOptionsFactory;
    private SmartBarInterfaceFactory $smartBarFactory;

    /**
     * @param ClientDataInterfaceFactory $clientDataFactory
     * @param VariantOptionsInterfaceFactory $variantOptionsFactory
     * @param SmartBarInterfaceFactory $smartBarFactory
     * @param array<self::FIELD_*, mixed> $data
     */
    public function __construct(
        ClientDataInterfaceFactory $clientDataFactory,
        VariantOptionsInterfaceFactory $variantOptionsFactory,
        SmartBarInterfaceFactory $smartBarFactory,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->clientDataFactory = $clientDataFactory;
        $this->variantOptionsFactory = $variantOptionsFactory;
        $this->smartBarFactory = $smartBarFactory;
    }

    public function getIndexName(): string
    {
        return (string)$this->_get(self::FIELD_INDEX_NAME);
    }

    public function setIndexName(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_INDEX_NAME, $value);
    }

    public function getQuery(): string
    {
        return (string)$this->_get(self::FIELD_QUERY);
    }

    public function setQuery(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_QUERY, $value);
    }

    public function getVariant(): VariantOptionsInterface
    {
        return $this->_get(self::FIELD_VARIANT) ?? $this->variantOptionsFactory->create();
    }

    public function setVariant(?VariantOptionsInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_VARIANT, $value);
    }

    public function getBoostQueries(): array
    {
        $value = (array)($this->_get(self::FIELD_BOOST_QUERIES) ?? []);
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            BoostQueryInterface::class
        );

        return $value;
    }

    public function setBoostQueries(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_BOOST_QUERIES, $value);
    }

    public function getDistanceUnitType(): int
    {
        return (int)$this->_get(self::FIELD_DISTANCE_UNIT_TYPE);
    }

    public function setDistanceUnitType(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_DISTANCE_UNIT_TYPE, $value);
    }

    public function getRequestType(): int
    {
        return (int)$this->_get(self::FIELD_REQUEST_TYPE);
    }

    public function setRequestType(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_REQUEST_TYPE, $value);
    }

    public function getImageData(): string
    {
        return (string)$this->_get(self::FIELD_IMAGE_DATA);
    }

    public function setImageData(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IMAGE_DATA, $value);
    }

    public function getImageText(): string
    {
        return (string)$this->_get(self::FIELD_IMAGE_TEXT);
    }

    public function setImageText(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IMAGE_TEXT, $value);
    }

    public function getKValue(): int
    {
        return (int)$this->_get(self::FIELD_K_VALUE);
    }

    public function setKValue(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_K_VALUE, $value);
    }

    public function getClientGuid(): string
    {
        return (string)$this->_get(self::FIELD_CLIENT_GUID);
    }

    public function setClientGuid(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CLIENT_GUID, $value);
    }

    public function getKeyword(): string
    {
        return (string)$this->_get(self::FIELD_KEYWORD);
    }

    public function setKeyword(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_KEYWORD, $value);
    }

    public function getPageId(): int
    {
        return (int)$this->_get(self::FIELD_PAGE_ID);
    }

    public function setPageId(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGE_ID, $value);
    }

    public function getPageNo(): int
    {
        return (int)$this->_get(self::FIELD_PAGE_NO);
    }

    public function setPageNo(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGE_NO, $value);
    }

    public function getMaxPerPage(): int
    {
        return (int)$this->_get(self::FIELD_MAX_PER_PAGE);
    }

    public function setMaxPerPage(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_MAX_PER_PAGE, $value);
    }

    public function getSearchWithin(): string
    {
        return (string)$this->_get(self::FIELD_SEARCH_WITHIN);
    }

    public function setSearchWithin(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SEARCH_WITHIN, $value);
    }

    public function getSortBy(): string
    {
        return (string)$this->_get(self::FIELD_SORT_BY);
    }

    public function setSortBy(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SORT_BY, $value);
    }

    public function getSortingSetCode(): string
    {
        return (string)$this->_get(self::FIELD_SORTING_SET_CODE);
    }

    public function setSortingSetCode(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SORTING_SET_CODE, $value);
    }

    public function getPaginationSetCode(): string
    {
        return (string)$this->_get(self::FIELD_PAGINATION_SET_CODE);
    }

    public function setPaginationSetCode(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGINATION_SET_CODE, $value);
    }

    public function getFacetSelections(): array
    {
        return (array)($this->_get(self::FIELD_FACET_SELECTIONS) ?? []);
    }

    public function setFacetSelections(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FACET_SELECTIONS, $value);
    }

    public function getCustomUrl(): string
    {
        return (string)$this->_get(self::FIELD_CUSTOM_URL);
    }

    public function setCustomUrl(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
    }

    public function getIsInPreview(): bool
    {
        return !!$this->_get(self::FIELD_IS_IN_PREVIEW);
    }

    public function setIsInPreview(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IS_IN_PREVIEW, $value);
    }

    public function getIs100CoverageTurnedOn(): bool
    {
        return !!$this->_get(self::FIELD_IS_100_COVERAGE_TURNED_ON);
    }

    public function setIs100CoverageTurnedOn(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IS_100_COVERAGE_TURNED_ON, $value);
    }

    public function getExplainDocId(): string
    {
        return (string)$this->_get(self::FIELD_EXPLAIN_DOC_ID);
    }

    public function setExplainDocId(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_EXPLAIN_DOC_ID, $value);
    }

    public function getFacetOverride(): array
    {
        return (array)($this->_get(self::FIELD_FACET_OVERRIDE) ?? []);
    }

    public function setFacetOverride(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FACET_OVERRIDE, $value);
    }

    public function getFieldOverride(): array
    {
        return (array)($this->_get(self::FIELD_FIELD_OVERRIDE) ?? []);
    }

    public function setFieldOverride(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FIELD_OVERRIDE, $value);
    }

    public function getSmartBar(): SmartBarInterface
    {
        return $this->_get(self::FIELD_SMART_BAR) ?? $this->smartBarFactory->create();
    }

    public function setSmartBar(?SmartBarInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SMART_BAR, $value);
    }

    public function getClientData(): ClientDataInterface
    {
        return $this->_get(self::FIELD_CLIENT_DATA) ?? $this->clientDataFactory->create();
    }

    public function setClientData(?ClientDataInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CLIENT_DATA, $value);
    }

    public function getSearchType(): string
    {
        return (string)$this->_get(self::FIELD_SEARCH_TYPE);
    }

    public function setSearchType(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SEARCH_TYPE, $value);
    }

    public function getIgnoreSpellcheck(): bool
    {
        return !!$this->_get(self::FIELD_IGNORE_SPELLCHECK);
    }

    public function setIgnoreSpellcheck(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IGNORE_SPELLCHECK, $value);
    }
}
