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

namespace HawkSearch\EsIndexing\Plugin\Indexing\Entity\Product;

use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\App\ObjectManager;

class EntityRebuildPlugin
{
    /**
     * @var StockRegistryStorage
     */
    private $stockRegistryStorage;

    /**
     * @param StockRegistryStorage|null $stockRegistryStorage
     */
    public function __construct(
        ?StockRegistryStorage $stockRegistryStorage = null
    ) {
        $this->stockRegistryStorage = $stockRegistryStorage ?: ObjectManager::getInstance()
            ->get(StockRegistryStorage::class);
    }

    /**
     * Clean shared objects data
     *
     * @return void
     */
    public function afterRebuild()
    {
        $this->stockRegistryStorage->clean();
    }
}
