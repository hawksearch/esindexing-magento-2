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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\EsIndexInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class EsIndex extends AbstractSimpleObject implements EsIndexInterface
{
    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return (string)$this->_get(self::INDEX_NAME);
    }

    public function setIndexName(string $value)
    {
        return $this->setData(self::INDEX_NAME, $value);
    }
}
