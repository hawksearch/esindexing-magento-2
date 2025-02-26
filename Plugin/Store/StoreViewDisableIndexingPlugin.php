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

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class StoreViewDisableIndexingPlugin
{
    private IndexingConfig $indexingConfig;
    private WriterInterface $configWriter;
    private ReinitableConfigInterface $reinitableConfig;

    public function __construct(
        IndexingConfig $indexingConfig,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig
    ) {
        $this->indexingConfig = $indexingConfig;
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
    }

    /**
     * Disable indexing for new store view
     *
     * @param StoreResourceModel $subject
     * @param StoreResourceModel $result
     * @param Store $store
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        StoreResourceModel $subject,
        StoreResourceModel $result,
        AbstractModel $store
    ): StoreResourceModel {
        if ($store->isObjectNew() && $this->indexingConfig->isIndexingEnabled($store->getId())) {
            $this->configWriter->save(
                $this->indexingConfig->getPath(IndexingConfig::CONFIG_ENABLE_INDEXING),
                (string)IndexingConfig::ENABLE_INDEXING_DEFAULT,
                ScopeInterface::SCOPE_STORES,
                $store->getId()
            );
            $this->reinitableConfig->reinit();
        }

        return $result;
    }
}
