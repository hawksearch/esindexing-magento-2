<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;

/**
 * @api
 * @since 0.8.0
 */
class LandingPageManagement implements LandingPageManagementInterface
{
    /**
     * @var InstructionManagerPoolInterface<string, InstructionManagerInterface>
     */
    private InstructionManagerPoolInterface $instructionManagerPool;

    /**
     * LandingPageManagement constructor.
     *
     * @param InstructionManagerPoolInterface<string, InstructionManagerInterface> $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPoolInterface $instructionManagerPool
    )
    {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    public function getLandingPages()
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getLandingPages')->get();
    }

    public function getLandingPageUrls()
    {
        return $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getLandingPageUrls')->get();
    }

    public function addLandingPages(array $landingPages)
    {
        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addLandingPagesBulk', $landingPages)->get();
    }

    public function updateLandingPages(array $landingPages)
    {
        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('updateLandingPagesBulk', $landingPages)->get();
    }

    public function deleteLandingPages(array $landingPageIds, bool $safeDelete = false)
    {
        if (!$landingPageIds) {
            return;
        }

        if ($safeDelete) {
            $allLandingPages = $this->getLandingPages();
            $allLandingPagesIds = [];
            foreach ($allLandingPages as $landingPage) {
                $allLandingPagesIds[] = $landingPage->getPageId();
            }

            $landingPageIds = array_intersect($landingPageIds, $allLandingPagesIds);
        }

        $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('deleteLandingPagesBulk', array_values($landingPageIds))->get();
    }
}
