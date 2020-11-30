<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Api\SearchCriteria\JoinProcessor;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use Magento\Store\Model\StoreManagerInterface;

class ReviewRatingSummary implements CustomJoinInterface
{
    /**
     * @var SummaryFactory
     */
    private $sumResourceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ReviewRatingSummary constructor.
     * @param SummaryFactory $sumResourceFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SummaryFactory $sumResourceFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->sumResourceFactory = $sumResourceFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     * @param ProductCollection $collection
     */
    public function apply(AbstractDb $collection)
    {
        //TODO: check if review_summary attribute is selected for indexing
        $this->sumResourceFactory->create()->appendSummaryFieldsToCollection(
            $collection,
            (string)$this->storeManager->getStore()->getId(),
            \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
        );
    }
}
