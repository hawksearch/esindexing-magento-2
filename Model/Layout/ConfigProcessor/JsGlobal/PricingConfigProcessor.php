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
    /**
     * @deprecated Backend price template generation is deprecated. All price rendering is now handled by the frontend Vue component. Only raw price data and config are provided.
     */
    public function process(array $jsConfig)
    {
        // DEPRECATED: All price template generation is now handled in the frontend (Vue component)
        // This method now only provides price config and format, not HTML or template strings.
        $jsConfig['pricing'] = [
            'currencyCode' => $this->storeManager->getStore()->getCurrentCurrencyCode(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'currencyFormat' => $this->storeManager->getStore()->getCurrentCurrency()->getOutputFormat(),
            // No patterns or priceTemplates provided anymore
        ];
        return $jsConfig;
    }
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
