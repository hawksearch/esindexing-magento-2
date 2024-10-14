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

use HawkSearch\EsIndexing\Api\Data\SmartBarInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class SmartBar extends AbstractSimpleObject implements SmartBarInterface
{

    /**
     * @inheritDoc
     */
    public function getBoostAndBury(): bool
    {
        return !!$this->_get(self::FIELD_BOOST_AND_BURY);
    }

    /**
     * @inheritDoc
     */
    public function setBoostAndBury(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_BOOST_AND_BURY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVisibilityRules(): bool
    {
        return !!$this->_get(self::FIELD_VISIBILITY_RULES);
    }

    /**
     * @inheritDoc
     */
    public function setVisibilityRules(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_VISIBILITY_RULES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPersonalizedBoost(): bool
    {
        return !!$this->_get(self::FIELD_PERSONALIZED_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setPersonalizedBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_PERSONALIZED_BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPopularityBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setPopularityBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItemPin(): bool
    {
        return !!$this->_get(self::FIELD_ITEM_PIN);
    }

    /**
     * @inheritDoc
     */
    public function setItemPin(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_ITEM_PIN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPopularitySalesBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_SALES_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setPopularitySalesBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_SALES_BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPopularityAdd2CartBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_ADD2_CART_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setPopularityAdd2CartBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_ADD2_CART_BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPopularityLandingPageBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_LANDING_PAGE_BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setPopularityLandingPageBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_LANDING_PAGE_BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKeywordReplacement(): bool
    {
        return !!$this->_get(self::FIELD_KEYWORD_REPLACEMENT);
    }

    /**
     * @inheritDoc
     */
    public function setKeywordReplacement(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_KEYWORD_REPLACEMENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewDate(): string
    {
        return (string)$this->_get(self::FIELD_PREVIEW_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setPreviewDate(?string $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_PREVIEW_DATE, $value);
    }
}
