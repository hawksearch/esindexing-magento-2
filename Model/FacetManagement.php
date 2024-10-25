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

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerInterface;
use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPoolInterface;
use HawkSearch\EsIndexing\Api\Data\FacetInterface;
use HawkSearch\EsIndexing\Api\FacetManagementInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @api
 * @since 0.8.0
 */
class FacetManagement implements FacetManagementInterface
{
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;

    /**
     * FacetManagement constructor.
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool
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
            ->executeByCode('addFacet', $this->collectFacetData($facet))->get();

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
            ->executeByCode('updateFacet', $this->collectFacetData($facet))->get();

        if (!$returnedFacet->getFacetId()) {
            throw new CouldNotSaveException(
                __('Could not save facet %1', $facet->getName())
            );
        }

        return $returnedFacet;
    }

    private function collectFacetData(FacetInterface $facet): array
    {
        if ($facet instanceof AbstractSimpleObject) {
            return $facet->__toArray();
        } else {
            throw new \InvalidArgumentException(
                __(
                    'Argument %1 passed to %2 should be an instance of %3 but %4 is given',
                    '$facet',
                    __METHOD__,
                    AbstractSimpleObject::class,
                    get_class($facet)
                )->render()
            );
        }
    }
}
