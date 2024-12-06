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

use HawkSearch\EsIndexing\Api\Data\VariantOptionsInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class VariantOptions extends AbstractSimpleObject implements VariantOptionsInterface
{

    /**
     * @inheritDoc
     */
    public function getCountFacetHitOnChild(): bool
    {
        return !!$this->_get(self::FIELD_COUNT_FACET_HIT_ON_CHILD);
    }

    /**
     * @inheritDoc
     */
    public function setCountFacetHitOnChild(bool $value): self
    {
        return $this->setData(self::FIELD_COUNT_FACET_HIT_ON_CHILD, $value);
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
    public function setPageNo(int $value): self
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
    public function setMaxPerPage(int $value): self
    {
        return $this->setData(self::FIELD_MAX_PER_PAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortCode(): string
    {
        return (string)$this->_get(self::FIELD_SORT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSortCode(?string $value): self
    {
        return $this->setData(self::FIELD_SORT_CODE, $value);
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
    public function setSortBy(?string $value): self
    {
        return $this->setData(self::FIELD_SORT_BY, $value);
    }
}
