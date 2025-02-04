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

    public function getBoostAndBury(): bool;

    public function setBoostAndBury(bool $value): self;

    public function getVisibilityRules(): bool;

    public function setVisibilityRules(bool $value): self;

    public function getPersonalizedBoost(): bool;

    public function setPersonalizedBoost(bool $value): self;

    public function getPopularityBoost(): bool;

    public function setPopularityBoost(bool $value): self;

    public function getItemPin(): bool;

    public function setItemPin(bool $value): self;

    public function getPopularitySalesBoost(): bool;

    public function setPopularitySalesBoost(bool $value): self;

    public function getPopularityAdd2CartBoost(): bool;

    public function setPopularityAdd2CartBoost(bool $value): self;

    public function getPopularityLandingPageBoost(): bool;

    public function setPopularityLandingPageBoost(bool $value): self;

    public function getKeywordReplacement(): bool;

    public function setKeywordReplacement(bool $value): self;

    public function getPreviewDate(): string;
    
    public function setPreviewDate(?string $value): self;

}
