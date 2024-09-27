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

namespace HawkSearch\EsIndexing\Model\Api\SearchCriteria\JoinProcessor;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
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
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * ReviewRatingSummary constructor.
     * @param SummaryFactory $sumResourceFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        SummaryFactory $sumResourceFactory,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->sumResourceFactory = $sumResourceFactory;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritDoc
     * @param ProductCollection $collection
     * @throws LocalizedException
     */
    public function apply(AbstractDb $collection)
    {
        $storeId = $collection->getStoreId();
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '<')) {
            $storeId = (string)$storeId;
        }

        //TODO: check if review_summary attribute is selected for indexing
        //TODO: push reviews data to the index https://bridgeline.atlassian.net/browse/HC-1693
        //Since we do not push reviews data to the index yet disable data collecting as well
        $addReviewData = false;

        if ($addReviewData) {
            $this->sumResourceFactory->create()->appendSummaryFieldsToCollection(
                $collection,
                $storeId,
                \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
            );
        }
    }
}
