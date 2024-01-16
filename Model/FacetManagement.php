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

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\FacetManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class FacetManagement implements FacetManagementInterface
{
    /**
     * @var InstructionManagerPool
     */
    private InstructionManagerPool $instructionManagerPool;

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
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getFacets')->get();
    }

    /**
     * @inheritDoc
     */
    public function addFacet(FacetInterface $facet): FacetInterface
    {
        /** @var FacetInterface $returnedFacet */
        $returnedFacet = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addFacet', $facet->__toArray())->get();

        if (!$returnedFacet->getFacetId()) {
            throw new CouldNotSaveException(
                __('Could not save facet %1', $facet->getName())
            );
        }

        return $returnedFacet;
    }

    /**
     * @inheritDoc
     */
    public function updateFacet(FacetInterface $facet): FacetInterface
    {
        /** @var FacetInterface $returnedFacet */
        $returnedFacet = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('updateFacet', $facet->__toArray())->get();

        if (!$returnedFacet->getFacetId()) {
            throw new CouldNotSaveException(
                __('Could not save facet %1', $facet->getName())
            );
        }

        return $returnedFacet;
    }
}
