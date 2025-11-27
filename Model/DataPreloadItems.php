<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

use Magento\Framework\Model\AbstractModel;

/**
 * @method $this setIndexId(int $value)
 * @method int getIndexId()
 * @method $this setMethod(string $value)
 * @method string getMethod()
 * @method $this setStatus(int $value)
 * @method int getStatus()
 * @method $this setRequest(string $value)
 * @method string getRequest()
 */
class DataPreloadItems extends AbstractModel
{
    public const STATUS_TYPE_COMPLETE = 1;
    public const STATUS_TYPE_FAILED = 2;
    public const STATUS_TYPE_OPEN = 4;
    public const STATUS_TYPE_REJECTED = 5;

    protected function _construct(): void
    {
        $this->_init(ResourceModel\DataPreloadItems::class);
    }
}
