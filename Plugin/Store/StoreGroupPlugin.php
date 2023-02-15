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

namespace HawkSearch\EsIndexing\Plugin\Store;

use HawkSearch\EsIndexing\Plugin\Product\AbstractPlugin;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ResourceModel\Group as StoreGroupResourceModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;

class StoreGroupPlugin extends AbstractPlugin
{
    /**
     * Invalidate indexer on store group save
     *
     * @param StoreGroupResourceModel $subject
     * @param StoreGroupResourceModel $result
     * @param AbstractModel $group
     * @return StoreGroupResourceModel
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(StoreGroupResourceModel $subject, StoreGroupResourceModel $result, AbstractModel $group)
    {
        if (!$group->isObjectNew() && $group->dataHasChangedFor('website_id')) {
            $this->productIndexer->invalidate();
        }

        return $result;
    }

    /**
     * Invalidate indexer on store view delete
     *
     * @param StoreGroupResourceModel $subject
     * @param StoreGroupResourceModel $result
     * @return StoreGroupResourceModel
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(StoreGroupResourceModel $subject, StoreGroupResourceModel $result)
    {
        $this->productIndexer->invalidate();

        return $result;
    }
}
