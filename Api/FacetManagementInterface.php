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

namespace HawkSearch\EsIndexing\Api;

use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface for managing Facets in HawkSearch
 *
 * @api
 * @since 0.8.0
 */
interface FacetManagementInterface
{
    /**
     * @return FacetInterface[]
     */
    public function getFacets(): array;

    /**
     * @return FacetInterface
     * @throws CouldNotSaveException
     */
    public function addFacet(FacetInterface $facet): FacetInterface;

    /**
     * @return FacetInterface
     * @throws CouldNotSaveException
     */
    public function updateFacet(FacetInterface $facet): FacetInterface;
}
