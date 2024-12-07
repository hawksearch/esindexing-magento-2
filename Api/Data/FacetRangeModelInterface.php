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
 * @see https://developerdocs.hawksearch.com/reference/facet_post_value
 * @see https://dev.hawksearch.net/swagger/ui/index#!/Facet/Facet_Post_value
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FacetRangeModelInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const RANGE_ID = 'RangeId';
    const NAME = 'Name';
    const L_BOUND = 'LBound';
    const U_BOUND = 'UBound';
    const SORT_ORDER = 'SortOrder';
    const ASSET_NAME = 'AssetName';
    const ASSET_URL = 'AssetUrl';
    /**#@-*/

    /**
     * @return int
     */
    public function getRangeId() : int;

    /**
     * @param int $value
     * @return $this
     */
    public function setRangeId(int $value): FacetRangeModelInterface;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setName(?string $value): FacetRangeModelInterface;

    /**
     * @return string
     */
    public function getLBound() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setLBound(?string $value): FacetRangeModelInterface;

    /**
     * @return string
     */
    public function getUBound() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setUBound(?string $value): FacetRangeModelInterface;

    /**
     * @return int
     */
    public function getSortOrder() : int;

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder(int $value): FacetRangeModelInterface;

    /**
     * @return string
     */
    public function getAssetName() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAssetName(?string $value): FacetRangeModelInterface;

    /**
     * @return string
     */
    public function getAssetUrl() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAssetUrl(?string $value): FacetRangeModelInterface;
}
