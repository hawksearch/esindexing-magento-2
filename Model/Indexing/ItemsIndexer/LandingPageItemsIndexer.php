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

namespace HawkSearch\EsIndexing\Model\Indexing\ItemsIndexer;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;

/**
 * The items indexer used for updating landing pages items changes in Hawksearch Dashboard
 */
class LandingPageItemsIndexer implements ItemsIndexerInterface
{
    /**
     * @var LandingPageManagementInterface
     */
    private $landingPageManagement;

    /**
     * @param LandingPageManagementInterface $landingPageManagement
     */
    public function __construct(
        LandingPageManagementInterface $landingPageManagement
    ) {
        $this->landingPageManagement = $landingPageManagement;
    }

    /**
     * @param LandingPageInterface[] $items
     * @inheritDoc
     */
    public function add(array $items, string $indexName)
    {
        $this->landingPageManagement->addLandingPages($items);
    }

    /**
     * Uses hierarchy API to upsert hierarchy items
     *
     * @param LandingPageInterface[] $items
     * @inheritDoc
     */
    public function update(array $items, string $indexName)
    {
        $this->landingPageManagement->updateLandingPages($items);
    }

    /**
     * Uses hierarchy API to remove hierarchy items
     *
     * @inheritDoc
     */
    public function delete(array $items, string $indexName)
    {
        $this->landingPageManagement->deleteLandingPages($items);
    }
}
