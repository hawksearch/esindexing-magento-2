<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use HawkSearch\EsIndexing\Api\Data\ClientDataInterface;
use HawkSearch\EsIndexing\Api\Data\FacetSelectionsInterface;
use HawkSearch\EsIndexing\Api\Data\SearchQueryInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class SearchQuery extends AbstractSimpleObject implements SearchQueryInterface
{
    /**
     * @inheritDoc
     */
    public function getClientGuid(): ?string
    {
        return $this->_get(self::FIELD_CLIENT_GUID);
    }

    /**
     * @inheritDoc
     */
    public function setClientGuid(string $value)
    {
        return $this->setData(self::FIELD_CLIENT_GUID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): ?string
    {
        return $this->_get(self::FIELD_KEYWORD);
    }

    /**
     * @inheritDoc
     */
    public function setKeyword(string $value)
    {
        return $this->setData(self::FIELD_KEYWORD, $value);
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
    public function setCustomUrl(string $value)
    {
        return $this->setData(self::FIELD_CUSTOM_URL, $value);
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
    public function setPageNo(int $value)
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
    public function setMaxPerPage(int $value)
    {
        return $this->setData(self::FIELD_MAX_PER_PAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortBy(): ?string
    {
        return $this->_get(self::FIELD_SORT_BY);
    }

    /**
     * @inheritDoc
     */
    public function setSortBy(string $value)
    {
        return $this->setData(self::FIELD_SORT_BY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortingSetCode(): ?string
    {
        return $this->_get(self::FIELD_SORTING_SET_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSortingSetCode(string $value)
    {
        return $this->setData(self::FIELD_SORTING_SET_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSearchWithin(): ?string
    {
        return $this->_get(self::FIELD_SEARCH_WITHIN);
    }

    /**
     * @inheritDoc
     */
    public function setSearchWithin(string $value)
    {
        return $this->setData(self::FIELD_SEARCH_WITHIN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetSelections(): FacetSelectionsInterface
    {
        return $this->_get(self::FIELD_FACET_SELECTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setFacetSelections(FacetSelectionsInterface $value)
    {
        return $this->setData(self::FIELD_FACET_SELECTIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetOverride(): array
    {
        return (array)$this->_get(self::FIELD_FACET_OVERRIDE);
    }

    /**
     * @inheritDoc
     */
    public function setFacetOverride(array $value)
    {
        return $this->setData(self::FIELD_FACET_OVERRIDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFieldOverride(): array
    {
        return $this->_get(self::FIELD_FIELD_OVERRIDE);
    }

    /**
     * @inheritDoc
     */
    public function setFieldOverride(array $value)
    {
        return (array)$this->setData(self::FIELD_FIELD_OVERRIDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getClientData(): ClientDataInterface
    {
        return $this->_get(self::FIELD_CLIENT_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setClientData(ClientDataInterface $value)
    {
        return $this->setData(self::FIELD_CLIENT_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function is100CoverageTurnedOn(): bool
    {
        return (bool)$this->_get(self::FIELD_IS_100_COVERAGE_TURNED_ON);
    }

    /**
     * @inheritDoc
     */
    public function setIs100CoverageTurnedOn(bool $value)
    {
        return $this->setData(self::FIELD_IS_100_COVERAGE_TURNED_ON, $value);
    }
}
