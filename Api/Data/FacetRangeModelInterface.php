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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * FacetRangeModel Interface used in Facet
 *
 * @api v11
 * @since 0.8.0
 * @link https://developerdocs.hawksearch.com/reference/facet_post_value
 * @link https://dev.hawksearch.net/swagger/ui/index#!/Facet/Facet_Post_value
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FacetRangeModelInterface
{
    const RANGE_ID = 'RangeId';
    const NAME = 'Name';
    const L_BOUND = 'LBound';
    const U_BOUND = 'UBound';
    const SORT_ORDER = 'SortOrder';
    const ASSET_NAME = 'AssetName';
    const ASSET_URL = 'AssetUrl';

    public function getRangeId(): int;

    public function setRangeId(int $value): self;

    public function getName(): string;

    public function setName(?string $value): self;

    public function getLBound(): string;

    public function setLBound(?string $value): self;

    public function getUBound(): string;

    public function setUBound(?string $value): self;

    public function getSortOrder(): int;

    public function setSortOrder(int $value): self;

    public function getAssetName(): string;

    public function setAssetName(?string $value): self;

    public function getAssetUrl(): string;

    public function setAssetUrl(?string $value): self;
}
