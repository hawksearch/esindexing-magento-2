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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\FacetManagementInterface;

class FacetManagement implements FacetManagementInterface
{
    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * FacetManagement constructor.
     * @param InstructionManagerPool $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPool $instructionManagerPool
    ){
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @inheritDoc
     */
    public function getFacets(): array
    {
        /*return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getFacets')->get();*/
        // TODO: to be implemented
        return [];
    }

    /**
     * @inheritDoc
     */
    public function addFacet(FacetInterface $facet): FacetInterface
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addFacet', $facet->__toArray())->get();
    }
}
