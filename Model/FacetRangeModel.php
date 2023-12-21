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

    /**
     * @inheritDoc
     */
    public function getRangeId(): int
    {
        return (int)$this->_get(self::RANGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRangeId(int $value): FacetRangeModelInterface
    {
        return $this->setData(self::RANGE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->_get(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLBound(): string
    {
        return (string)$this->_get(self::L_BOUND);
    }

    /**
     * @inheritDoc
     */
    public function setLBound(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::L_BOUND, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUBound(): string
    {
        return (string)$this->_get(self::U_BOUND);
    }

    /**
     * @inheritDoc
     */
    public function setUBound(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::U_BOUND, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $value): FacetRangeModelInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAssetName(): string
    {
        return (string)$this->_get(self::ASSET_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAssetName(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::ASSET_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAssetUrl(): string
    {
        return (string)$this->_get(self::ASSET_URL);
    }

    /**
     * @inheritDoc
     */
    public function setAssetUrl(?string $value): FacetRangeModelInterface
    {
        return $this->setData(self::ASSET_URL, $value);
    }
}
