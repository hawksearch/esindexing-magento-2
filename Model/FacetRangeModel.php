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

use HawkSearch\EsIndexing\Api\Data\FacetRangeModelInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class FacetRangeModel extends AbstractSimpleObject implements FacetRangeModelInterface
{

    public function getRangeId(): int
    {
        return (int)$this->_get(self::RANGE_ID);
    }

    public function setRangeId(int $value): FacetRangeModelInterface
    {
        return $this->setData(self::RANGE_ID, $value);
    }

    public function getName(): string
    {
        return (string)$this->_get(self::NAME);
    }

    public function setName(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::NAME, $value);
    }

    public function getLBound(): string
    {
        return (string)$this->_get(self::L_BOUND);
    }

    public function setLBound(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::L_BOUND, $value);
    }

    public function getUBound(): string
    {
        return (string)$this->_get(self::U_BOUND);
    }

    public function setUBound(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::U_BOUND, $value);
    }

    public function getSortOrder(): int
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    public function setSortOrder(int $value): FacetRangeModelInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    public function getAssetName(): string
    {
        return (string)$this->_get(self::ASSET_NAME);
    }

    public function setAssetName(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::ASSET_NAME, $value);
    }

    public function getAssetUrl(): string
    {
        return (string)$this->_get(self::ASSET_URL);
    }

    public function setAssetUrl(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::ASSET_URL, $value);
    }
}
