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

namespace HawkSearch\EsIndexing\Model\ContentPage\Field\Handler;

use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use Magento\Cms\Model\Page as PageModel;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @implements FieldHandlerInterface<PageModel>
 */
class Url implements FieldHandlerInterface
{

    private StoreManagerInterface $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl() . $item->getIdentifier();
        //return $item->getIdentifier();
    }
}
