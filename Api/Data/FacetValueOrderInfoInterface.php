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
 * @api v11
 * @see https://developerdocs.hawksearch.com/docs/facet-object#facet-value-order-info
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FacetValueOrderInfoInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const VALUE = 'Value';
    const SORT_ORDER = 'SortOrder';
    /**#@-*/

    /**
     * @return string
     */
    public function getValue() : string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setValue(?string $value): FacetValueOrderInfoInterface;

    /**
     * @return int
     */
    public function getSortOrder() : int;

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder(int $value): FacetValueOrderInfoInterface;
}
