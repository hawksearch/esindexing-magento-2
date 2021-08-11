<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;

class LandingPageManagement implements LandingPageManagementInterface
{
    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * LandingPageManagement constructor.
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
    public function getLandingPages()
    {
        return $hawkFieldsResponse = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getLandingPages')->get();
    }

    /**
     * @inheritDoc
     */
    public function getLandingPageUrls()
    {
        return $hawkFieldsResponse = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('getLandingPageUrls')->get();
    }

    /**
     * @inheritDoc
     */
    public function addLandingPages(array $landingPages)
    {
        $hawkFieldsResponse = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('addLandingPagesBulk', $landingPages)->get();
    }

    /**
     * @inheritDoc
     */
    public function updateLandingPages(array $landingPages)
    {
        return $hawkFieldsResponse = $this->instructionManagerPool->get('hawksearch-esindexing')
            ->executeByCode('updateLandingPagesBulk', $landingPages)->get();
    }

    /**
     * @inheritDoc
     */
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
