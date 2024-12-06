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

use HawkSearch\EsIndexing\Api\Data\BoostQueryInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class BoostQuery extends AbstractSimpleObject implements BoostQueryInterface
{

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return (string)$this->_get(self::FIELD_QUERY);
    }

    /**
     * @inheritDoc
     */
    public function setQuery(?string $value): BoostQueryInterface
    {
        return $this->setData(self::FIELD_QUERY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBoost(): float
    {
        return (float)$this->_get(self::FIELD_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setBoost(float $value): BoostQueryInterface
    {
        return $this->setData(self::FIELD_BOOST, $value);
    }
}
