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
 * @method $this setStoreId(int $value)
 * @method int getStoreId()
 * @method $this setEngineIndexName(string $value)
 * @method string getEngineIndexName()
 * @method $this setIsValid(bool $value)
 * @method bool getIsValid()
 * @method $this setIsCurrent(bool $value)
 * @method bool getIsCurrent()
 * @method $this setUpdatedAt(?string $value)
 * @method string getUpdatedAt()
 * @method $this setCreatedAt(?string $value)
 * @method string getCreatedAt()
 * @method $this setIsStage1Complete(bool $value)
 * @method bool getIsStage1Complete()
 * @method $this setStage2Scheduled(int $value)
 * @method int getStage2Scheduled()
 * @method $this setStage2Completed(int $value)
 * @method int getStage2Completed()
 */
class DataIndex extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\DataIndex::class);
    }
}
