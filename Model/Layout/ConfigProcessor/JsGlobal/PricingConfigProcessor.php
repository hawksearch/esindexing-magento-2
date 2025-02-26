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

namespace HawkSearch\EsIndexing\Model\Layout\ConfigProcessor\JsGlobal;

use HawkSearch\EsIndexing\Helper\PricingHelper;
use HawkSearch\EsIndexing\Model\Layout\LayoutConfigProcessorInterface;
use Magento\Framework\Locale\Format as LocaleFormat;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Render product pricing related Javascript configurations
 */
class PricingConfigProcessor implements LayoutConfigProcessorInterface
{
    private LocaleFormat $localeFormat;
    private StoreManagerInterface $storeManager;
    private PricingHelper $pricingHelper;

    public function __construct(
        LocaleFormat $localeFormat,
        StoreManagerInterface $storeManager,
        PricingHelper $pricingHelper
    ) {
        $this->localeFormat = $localeFormat;
        $this->storeManager = $storeManager;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * Process configurations
     * Uses format supported by mage/utils/template templates syntax
     *
     * @return array<string, mixed>
     */
    public function process(array $jsConfig)
    {
        $priceTemplates = [];

        $fakeUid = PHP_INT_MAX;
        $fakeUidPattern = '${ $.uid }';
        $fakeExcludingTaxPriceRegularAmount = 100 * 1237;
        $fakeExcludingTaxPriceRegularAmountPattern = '${ $.price_regular }';
        $fakeExcludingTaxPriceRegularAmountFormattedPattern = '${ $.price_regular_formatted }';
        $fakeIncludingTaxPriceRegularAmount = $fakeExcludingTaxPriceRegularAmount * 1.1; // tax 10%
        $fakeIncludingTaxPriceRegularAmountPattern = '${ $.price_regular_include_tax }';
        $fakeIncludingTaxPriceRegularAmountFormattedPattern = '${ $.price_regular_include_tax_formatted }';
        $fakeExcludingTaxPriceAmount = $fakeExcludingTaxPriceRegularAmount * 50 / 100; // discounted price, 50% discount
        $fakeExcludingTaxPriceAmountPattern = '${ $.price_final }';
        $fakeExcludingTaxPriceAmountFormattedPattern = '${ $.price_final_formatted }';
        $fakeIncludingTaxPriceAmount = $fakeExcludingTaxPriceAmount * 1.1; // final price, tax 10%
        $fakeIncludingTaxPriceAmountPattern = '${ $.price_final_include_tax }';
        $fakeIncludingTaxPriceAmountFormattedPattern = '${ $.price_final_include_tax_formatted }';
        $fakeCurrencyCode = 'FAKECURRENCYCODE';
        $fakeCurrencyCodePattern = '${ $.currency_code }';
        $__ = '__';

        $exclTaxTpl = <<<TEMPLATE
<span id="price-excluding-tax-product-price-$fakeUid" data-label="{$__('Excl. Tax')}" data-price-amount="$fakeExcludingTaxPriceAmount" data-price-type="basePrice" class="price-wrapper price-excluding-tax">
  <span class="price">$fakeCurrencyCode$fakeExcludingTaxPriceAmount</span>
</span>
TEMPLATE;
        if (!$this->pricingHelper->getTaxHelper()->displayBothPrices()) {
            $exclTaxTpl = '';
        }

        // simple, downloadable, virtual, giftcard
        $priceTemplates['default']['regular'] = <<<TEMPLATE
<div class="price-box price-final_price" data-role="priceBox" data-product-id="$fakeUid" data-price-box="product-id-$fakeUid">
  <span class="price-container price-final_price tax weee">
    <span id="price-including-tax-product-price-$fakeUid" data-label="{$__('Incl. Tax')}" data-price-amount="$fakeIncludingTaxPriceAmount" data-price-type="finalPrice" class="price-wrapper price-including-tax">
      <span class="price">$fakeCurrencyCode$fakeIncludingTaxPriceAmount</span>
    </span>
    {$exclTaxTpl}
  </span>
</div>
TEMPLATE;
        $priceTemplates['default']['special'] = <<<TEMPLATE
<div class="price-box price-final_price" data-role="priceBox" data-product-id="$fakeUid" data-price-box="product-id-$fakeUid">
  <span class="special-price">
    <span class="price-container price-final_price tax weee">
      <span class="price-label">{$__('Special Price')}</span>
      <span id="price-including-tax-product-price-$fakeUid" data-label="{$__('Incl. Tax')}" data-price-amount="$fakeIncludingTaxPriceAmount" data-price-type="finalPrice" class="price-wrapper price-including-tax">
        <span class="price">$fakeCurrencyCode$fakeIncludingTaxPriceAmount</span>
      </span>
      {$exclTaxTpl}
    </span>
  </span>
  <span class="old-price">
    <span class="price-container price-final_price tax weee">
      <span class="price-label">{$__('Regular Price')}</span>
      <span id="price-including-tax-old-price-$fakeUid" data-label="{$__('Incl. Tax')}" data-price-amount="$fakeIncludingTaxPriceRegularAmount" data-price-type="oldPrice" class="price-wrapper price-including-tax">
        <span class="price">$fakeCurrencyCode$fakeIncludingTaxPriceRegularAmount</span>
      </span>
    </span>
  </span>
</div>
TEMPLATE;
        $priceTemplates['configurable']['regular'] =
        $priceTemplates['bundle']['regular'] =
        $priceTemplates['grouped']['regular'] = <<<TEMPLATE
<div class="price-box price-final_price" data-role="priceBox" data-product-id="$fakeUid" data-price-box="product-id-$fakeUid">
  <span class="normal-price">
    <span class="price-container price-final_price tax weee">
      <span class="price-label">{$__('As low as')}</span>
      <span id="price-including-tax-product-price-$fakeUid" data-label="{$__('Incl. Tax')}" data-price-amount="$fakeIncludingTaxPriceAmount" data-price-type="finalPrice" class="price-wrapper price-including-tax">
        <span class="price">$fakeCurrencyCode$fakeIncludingTaxPriceAmount</span>
      </span>
      {$exclTaxTpl}
    </span>
  </span>
</div>
TEMPLATE;

        foreach ($priceTemplates as $productType => $productTypeData) {
            foreach ($productTypeData as $priceType => $template) {
                $priceTemplates[$productType][$priceType] = str_replace(
                    [
                        $fakeUid,
                        $fakeCurrencyCode . $fakeExcludingTaxPriceRegularAmount,
                        $fakeCurrencyCode . $fakeIncludingTaxPriceRegularAmount,
                        $fakeCurrencyCode . $fakeExcludingTaxPriceAmount,
                        $fakeCurrencyCode . $fakeIncludingTaxPriceAmount,
                        $fakeExcludingTaxPriceRegularAmount,
                        $fakeIncludingTaxPriceRegularAmount,
                        $fakeExcludingTaxPriceAmount,
                        $fakeIncludingTaxPriceAmount,
                    ],
                    [
                        $fakeUidPattern,
                        $fakeExcludingTaxPriceRegularAmountFormattedPattern,
                        $fakeIncludingTaxPriceRegularAmountFormattedPattern,
                        $fakeExcludingTaxPriceAmountFormattedPattern,
                        $fakeIncludingTaxPriceAmountFormattedPattern,
                        $fakeExcludingTaxPriceRegularAmountPattern,
                        $fakeIncludingTaxPriceRegularAmountPattern,
                        $fakeExcludingTaxPriceAmountPattern,
                        $fakeIncludingTaxPriceAmountPattern,
                    ],
                    $template
                );
            }
        }

        $jsConfig['pricing'] = [
            'currencyCode' => $this->storeManager->getStore()->getCurrentCurrencyCode(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'currencyFormat' => $this->storeManager->getStore()->getCurrentCurrency()->getOutputFormat(),
            'patterns' => [
                'uid' => $fakeUidPattern,
                'excludingTaxPriceRegularAmount' => $fakeExcludingTaxPriceRegularAmountPattern,
                'includingTaxPriceRegularAmount' => $fakeIncludingTaxPriceRegularAmountPattern,
                'excludingTaxPriceAmount' => $fakeExcludingTaxPriceAmountPattern,
                'includingTaxPriceAmount' => $fakeIncludingTaxPriceAmountPattern,
                'excludingTaxPriceRegularFormattedAmount' => $fakeExcludingTaxPriceRegularAmountFormattedPattern,
                'includingTaxPriceRegularFormattedAmount' => $fakeIncludingTaxPriceRegularAmountFormattedPattern,
                'excludingTaxPriceAmountFormatted' => $fakeExcludingTaxPriceAmountFormattedPattern,
                'includingTaxPriceAmountFormatted' => $fakeIncludingTaxPriceAmountFormattedPattern,
            ],
            'priceTemplates' => $priceTemplates
        ];

        return $jsConfig;
    }
}
