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
    public function getBoostAndBury(): bool
    {
        return !!$this->_get(self::FIELD_BOOST_AND_BURY);
    }

    public function setBoostAndBury(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_BOOST_AND_BURY, $value);
    }

    public function getVisibilityRules(): bool
    {
        return !!$this->_get(self::FIELD_VISIBILITY_RULES);
    }

    public function setVisibilityRules(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_VISIBILITY_RULES, $value);
    }

    public function getPersonalizedBoost(): bool
    {
        return !!$this->_get(self::FIELD_PERSONALIZED_BOOST);
    }

    public function setPersonalizedBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_PERSONALIZED_BOOST, $value);
    }

    public function getPopularityBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_BOOST);
    }

    public function setPopularityBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_BOOST, $value);
    }

    public function getItemPin(): bool
    {
        return !!$this->_get(self::FIELD_ITEM_PIN);
    }

    public function setItemPin(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_ITEM_PIN, $value);
    }

    public function getPopularitySalesBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_SALES_BOOST);
    }

    public function setPopularitySalesBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_SALES_BOOST, $value);
    }

    public function getPopularityAdd2CartBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_ADD2_CART_BOOST);
    }

    public function setPopularityAdd2CartBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_ADD2_CART_BOOST, $value);
    }

    public function getPopularityLandingPageBoost(): bool
    {
        return !!$this->_get(self::FIELD_POPULARITY_LANDING_PAGE_BOOST);
    }

    public function setPopularityLandingPageBoost(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_POPULARITY_LANDING_PAGE_BOOST, $value);
    }

    public function getKeywordReplacement(): bool
    {
        return !!$this->_get(self::FIELD_KEYWORD_REPLACEMENT);
    }

    public function setKeywordReplacement(bool $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_KEYWORD_REPLACEMENT, $value);
    }

    public function getPreviewDate(): string
    {
        return (string)$this->_get(self::FIELD_PREVIEW_DATE);
    }

    public function setPreviewDate(?string $value): SmartBarInterface
    {
        return $this->setData(self::FIELD_PREVIEW_DATE, $value);
    }
}
