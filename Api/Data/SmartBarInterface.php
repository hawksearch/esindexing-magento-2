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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * SmartBar Interface used in SearchRequest
 */
interface SmartBarInterface
{
    /**#@+
     * Constants for keys of data array
     */
    public const FIELD_BOOST_AND_BURY = 'BoostAndBury';
    public const FIELD_VISIBILITY_RULES = 'VisibilityRules';
    public const FIELD_PERSONALIZED_BOOST = 'PersonalizedBoost';
    public const FIELD_POPULARITY_BOOST = 'PopularityBoost';
    public const FIELD_ITEM_PIN = 'ItemPin';
    public const FIELD_POPULARITY_SALES_BOOST = 'PopularitySalesBoost';
    public const FIELD_POPULARITY_ADD2_CART_BOOST = 'PopularityAdd2CartBoost';
    public const FIELD_POPULARITY_LANDING_PAGE_BOOST = 'PopularityLandingPageBoost';
    public const FIELD_KEYWORD_REPLACEMENT = 'KeywordReplacement';
    public const FIELD_PREVIEW_DATE = 'PreviewDate';
    /**#@-*/

    /**
     * @return bool
     */
    public function getBoostAndBury(): bool;

    /**
     * @return $this
     */
    public function setBoostAndBury(bool $value): self;

    /**
     * @return bool
     */
    public function getVisibilityRules(): bool;

    /**
     * @return $this
     */
    public function setVisibilityRules(bool $value): self;

    /**
     * @return bool
     */
    public function getPersonalizedBoost(): bool;

    /**
     * @return $this
     */
    public function setPersonalizedBoost(bool $value): self;

    /**
     * @return bool
     */
    public function getPopularityBoost(): bool;

    /**
     * @return $this
     */
    public function setPopularityBoost(bool $value): self;

    /**
     * @return bool
     */
    public function getItemPin(): bool;

    /**
     * @return $this
     */
    public function setItemPin(bool $value): self;

    /**
     * @return bool
     */
    public function getPopularitySalesBoost(): bool;

    /**
     * @return $this
     */
    public function setPopularitySalesBoost(bool $value): self;

    /**
     * @return bool
     */
    public function getPopularityAdd2CartBoost(): bool;

    /**
     * @return $this
     */
    public function setPopularityAdd2CartBoost(bool $value): self;

    /**
     * @return bool
     */
    public function getPopularityLandingPageBoost(): bool;

    /**
     * @return $this
     */
    public function setPopularityLandingPageBoost(bool $value): self;

    /**
     * @return bool
     */
    public function getKeywordReplacement(): bool;

    /**
     * @return $this
     */
    public function setKeywordReplacement(bool $value): self;

    /**
     * @return string
     */
    public function getPreviewDate(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPreviewDate(?string $value): self;

}
