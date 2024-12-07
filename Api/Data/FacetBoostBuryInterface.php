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
 * FacetBoostBury Interface used in Facet
 *
 * @api v11
 * @since 0.8.0
 * @see https://developerdocs.hawksearch.com/reference/facet_post_value
 * @see https://dev.hawksearch.net/swagger/ui/index#!/Facet/Facet_Post_value
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FacetBoostBuryInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const BOOST_VALUES = 'BoostValues';
    const BURY_VALUES = 'BuryValues';
    /**#@-*/

    /**
     * @return \HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface[]
     */
    public function getBoostValues() : array;

    /**
     * @param \HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface[]|null $value
     * @return $this
     */
    public function setBoostValues(?array $value): FacetBoostBuryInterface;

    /**
     * @return \HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface[]
     */
    public function getBuryValues() : array;

    /**
     * @param \HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface[]|null $value
     * @return $this
     */
    public function setBuryValues(?array $value): FacetBoostBuryInterface;

}
