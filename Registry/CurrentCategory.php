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

namespace HawkSearch\EsIndexing\Registry;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;

/**
 * Registry to access current active category on the page
 *
 * @api
 * @since 0.8.0
 */
class CurrentCategory
{
    private CategoryInterface $category;

    public function __construct(CategoryInterfaceFactory $categoryFactory)
    {
        $this->category = $categoryFactory->create();
    }

    /**
     * @return CategoryInterface
     */
    public function get(): CategoryInterface
    {
        return $this->category;
    }

    public function set(CategoryInterface $category)
    {
        $this->category = $category;
    }
}
