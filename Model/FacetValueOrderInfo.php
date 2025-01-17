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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class FacetValueOrderInfo extends AbstractSimpleObject implements FacetValueOrderInfoInterface
{

    public function getValue(): string
    {
        return (string)$this->_get(self::VALUE);
    }

    public function setValue(?string $value): FacetValueOrderInfoInterface
    {
        return $this->setData(self::VALUE, $value);
    }

    public function getSortOrder(): int
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    public function setSortOrder(int $value): FacetValueOrderInfoInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }
}
