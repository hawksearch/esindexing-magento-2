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
    /**
     * @var ClientDataInterfaceFactory
     */
    private ClientDataInterfaceFactory $clientDataFactory;

    /**
     * @var VariantOptionsInterfaceFactory
     */
    private VariantOptionsInterfaceFactory $variantOptionsFactory;

    /**
     * @var SmartBarInterfaceFactory
     */
    private SmartBarInterfaceFactory $smartBarFactory;

    /**
     * @param ClientDataInterfaceFactory $clientDataFactory
     * @param VariantOptionsInterfaceFactory $variantOptionsFactory
     * @param SmartBarInterfaceFactory $smartBarFactory
     * @param array $data
     */
    public function __construct(
        ClientDataInterfaceFactory $clientDataFactory,
        VariantOptionsInterfaceFactory $variantOptionsFactory,
        SmartBarInterfaceFactory $smartBarFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->clientDataFactory = $clientDataFactory;
        $this->variantOptionsFactory = $variantOptionsFactory;
        $this->smartBarFactory = $smartBarFactory;
    }

    /**
     * @inheritDoc
     */
    public function getIndexName(): string
    {
        return (string)$this->_get(self::FIELD_INDEX_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setIndexName(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_INDEX_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return (string)$this->_get(self::FIELD_QUERY);
    }

    /**
     * @inheritDoc
     */
    public function setQuery(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_QUERY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVariant(): VariantOptionsInterface
    {
        return $this->_get(self::FIELD_VARIANT) ?? $this->variantOptionsFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function setVariant(?VariantOptionsInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_VARIANT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBoostQueries(): array
    {
        $value = $this->_get(self::FIELD_BOOST_QUERIES) ?? [];
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            BoostQueryInterface::class
        );

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setBoostQueries(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_BOOST_QUERIES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDistanceUnitType(): int
    {
        return (int)$this->_get(self::FIELD_DISTANCE_UNIT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setDistanceUnitType(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_DISTANCE_UNIT_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRequestType(): int
    {
        return (int)$this->_get(self::FIELD_REQUEST_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setRequestType(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_REQUEST_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImageData(): string
    {
        return (string)$this->_get(self::FIELD_IMAGE_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setImageData(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IMAGE_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImageText(): string
    {
        return (string)$this->_get(self::FIELD_IMAGE_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setImageText(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IMAGE_TEXT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKValue(): int
    {
        return (int)$this->_get(self::FIELD_K_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setKValue(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_K_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getClientGuid(): string
    {
        return (string)$this->_get(self::FIELD_CLIENT_GUID);
    }

    /**
     * @inheritDoc
     */
    public function setClientGuid(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CLIENT_GUID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return (string)$this->_get(self::FIELD_KEYWORD);
    }

    /**
     * @inheritDoc
     */
    public function setKeyword(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_KEYWORD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPageId(): int
    {
        return (int)$this->_get(self::FIELD_PAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPageId(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPageNo(): int
    {
        return (int)$this->_get(self::FIELD_PAGE_NO);
    }

    /**
     * @inheritDoc
     */
    public function setPageNo(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGE_NO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMaxPerPage(): int
    {
        return (int)$this->_get(self::FIELD_MAX_PER_PAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMaxPerPage(int $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_MAX_PER_PAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSearchWithin(): string
    {
        return (string)$this->_get(self::FIELD_SEARCH_WITHIN);
    }

    /**
     * @inheritDoc
     */
    public function setSearchWithin(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SEARCH_WITHIN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortBy(): string
    {
        return (string)$this->_get(self::FIELD_SORT_BY);
    }

    /**
     * @inheritDoc
     */
    public function setSortBy(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SORT_BY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortingSetCode(): string
    {
        return (string)$this->_get(self::FIELD_SORTING_SET_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSortingSetCode(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SORTING_SET_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPaginationSetCode(): string
    {
        return (string)$this->_get(self::FIELD_PAGINATION_SET_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setPaginationSetCode(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_PAGINATION_SET_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetSelections(): array
    {
        return $this->_get(self::FIELD_FACET_SELECTIONS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFacetSelections(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FACET_SELECTIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomUrl(): string
    {
        return (string)$this->_get(self::FIELD_CUSTOM_URL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomUrl(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsInPreview(): bool
    {
        return !!$this->_get(self::FIELD_IS_IN_PREVIEW);
    }

    /**
     * @inheritDoc
     */
    public function setIsInPreview(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IS_IN_PREVIEW, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIs100CoverageTurnedOn(): bool
    {
        return !!$this->_get(self::FIELD_IS_100_COVERAGE_TURNED_ON);
    }

    /**
     * @inheritDoc
     */
    public function setIs100CoverageTurnedOn(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IS_100_COVERAGE_TURNED_ON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExplainDocId(): string
    {
        return (string)$this->_get(self::FIELD_EXPLAIN_DOC_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExplainDocId(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_EXPLAIN_DOC_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetOverride(): array
    {
        return $this->_get(self::FIELD_FACET_OVERRIDE) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFacetOverride(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FACET_OVERRIDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFieldOverride(): array
    {
        return $this->_get(self::FIELD_FIELD_OVERRIDE) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFieldOverride(?array $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_FIELD_OVERRIDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSmartBar(): SmartBarInterface
    {
        return $this->_get(self::FIELD_SMART_BAR) ?? $this->smartBarFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function setSmartBar(?SmartBarInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SMART_BAR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getClientData(): ClientDataInterface
    {
        return $this->_get(self::FIELD_CLIENT_DATA) ?? $this->clientDataFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function setClientData(?ClientDataInterface $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_CLIENT_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSearchType(): string
    {
        return (string)$this->_get(self::FIELD_SEARCH_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setSearchType(?string $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_SEARCH_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIgnoreSpellcheck(): bool
    {
        return !!$this->_get(self::FIELD_IGNORE_SPELLCHECK);
    }

    /**
     * @inheritDoc
     */
    public function setIgnoreSpellcheck(bool $value): SearchRequestInterface
    {
        return $this->setData(self::FIELD_IGNORE_SPELLCHECK, $value);
    }
}
