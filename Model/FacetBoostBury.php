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

use HawkSearch\EsIndexing\Api\Data\FacetBoostBuryInterface;
use HawkSearch\EsIndexing\Api\Data\FacetValueOrderInfoInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use Magento\Framework\Api\AbstractSimpleObject;

class FacetBoostBury extends AbstractSimpleObject implements FacetBoostBuryInterface
{

    /**
     * @throws \InvalidArgumentException
     */
    public function getBoostValues(): array
    {
        $value = (array)($this->_get(self::BOOST_VALUES) ?? []);
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            FacetValueOrderInfoInterface::class
        );

        return $value;
    }

    public function setBoostValues(?array $value): FacetBoostBuryInterface
    {
        return $this->setData(self::BOOST_VALUES, $value);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getBuryValues(): array
    {
        $value = (array)($this->_get(self::BURY_VALUES) ?? []);
        array_walk(
            $value,
            [ObjectHelper::class, 'validateObjectValue'],
            FacetValueOrderInfoInterface::class
        );

        return $value;
    }

    public function setBuryValues(?array $value): FacetBoostBuryInterface
    {
        return $this->setData(self::BURY_VALUES, $value);
    }
}
