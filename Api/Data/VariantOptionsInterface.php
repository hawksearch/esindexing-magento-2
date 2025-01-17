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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * VariantOptions Interface used in SearchRequest
 */
interface VariantOptionsInterface
{
    public const FIELD_COUNT_FACET_HIT_ON_CHILD = 'CountFacetHitOnChild';
    public const FIELD_PAGE_NO = 'PageNo';
    public const FIELD_MAX_PER_PAGE = 'MaxPerPage';
    public const FIELD_SORT_CODE = 'SortCode';
    public const FIELD_SORT_BY = 'SortBy';

    /**
     * @return bool
     */
    public function getCountFacetHitOnChild(): bool;

    /**
     * @return $this
     */
    public function setCountFacetHitOnChild(bool $value): self;

    /**
     * @return int
     */
    public function getPageNo(): int;

    /**
     * @return $this
     */
    public function setPageNo(int $value): self;

    /**
     * @return int
     */
    public function getMaxPerPage(): int;

    /**
     * @return $this
     */
    public function setMaxPerPage(int $value): self;

    /**
     * @return string
     */
    public function getSortCode(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSortCode(?string $value): self;

    /**
     * @return string
     */
    public function getSortBy(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSortBy(?string $value): self;
}
