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

use HawkSearch\EsIndexing\Model\Layout\LayoutConfigProcessorInterface;
use Magento\Framework\Locale\Format as LocaleFormat;

/**
 * Render product pricing related Javascript configurations
 */
class PricingConfigProcessor implements LayoutConfigProcessorInterface
{
    private LocaleFormat $localeFormat;

    public function __construct(
        LocaleFormat $localeFormat
    ) {
        $this->localeFormat = $localeFormat;
    }

    /**
     * @return array{
     *     priceFormat: array{
     *         pattern: string,
     *         precision: int,
     *         requiredPrecision: int,
     *         decimalSymbol: string,
     *         groupSymbol: string,
     *         groupLength: int|false,
     *         integerRequired: bool
     *     }
     * }
     */
    public function process(array $jsConfig)
    {
        $jsConfig['pricing'] = [
            'priceFormat' => $this->localeFormat->getPriceFormat(),
        ];
        return $jsConfig;
    }
}
