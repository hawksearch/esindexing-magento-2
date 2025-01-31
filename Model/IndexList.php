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

use HawkSearch\EsIndexing\Api\Data\IndexListInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class IndexList extends AbstractSimpleObject implements IndexListInterface
{

    public function getIndexNames(): array
    {
        return (array)$this->_get(self::FIELD_INDEX_NAMES);
    }

    public function setIndexNames(array $value)
    {
        return $this->setData(self::FIELD_INDEX_NAMES, $value);
    }
}
