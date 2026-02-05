/**
 * Copyright (c) 2026 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

/**
 * Example Usage and Manual Testing
 * Demonstrates how to use the refactored pricing components
 * Can be used for manual testing in browser console
 *
 * @module HawkSearch_EsIndexing/js/pricing/examples
 */

// Example 1: Simple Product with Discount (Excluding Tax)
var simpleProductWithDiscount = {
    type_id: 'simple',
    __uid: '123',
    price_regular: 45.00,
    price_final: 32.00,
    price_min: null,
    price_max: null
};

// Example 2: Simple Product without Discount (Including Tax)
var simpleProductNoDiscount = {
    type_id: 'simple',
    __uid: '456',
    price_regular: null,
    price_final: 29.99,
    price_min: null,
    price_max: null
};

// Example 3: Bundle Product with Range
var bundleProduct = {
    type_id: 'bundle',
    __uid: '789',
    price_regular: null,
    price_final: 50.00,
    price_min: 50.00,
    price_max: 150.00
};

// Example 4: Grouped Product
var groupedProduct = {
    type_id: 'grouped',
    __uid: '101',
    price_regular: null,
    price_final: 25.00,
    price_min: null,
    price_max: null
};

// Example 5: Configurable Product with Range
var configurableProductWithRange = {
    type_id: 'configurable',
    __uid: '202',
    price_regular: null,
    price_final: 75.00,
    price_min: 75.00,
    price_max: 125.00
};

// Example 6: Configurable Product without Range (with Discount)
var configurableProductDiscount = {
    type_id: 'configurable',
    __uid: '303',
    price_regular: 100.00,
    price_final: 80.00,
    price_min: null,
    price_max: null
};

// Example 7: Gift Card
var giftCardProduct = {
    type_id: 'giftcard',
    __uid: '404',
    price_regular: null,
    price_final: 50.00,
    price_min: null,
    price_max: null
};

// Tax Configuration Examples
var taxConfigExcluding = {
    displayMode: 'excluding_tax',
    taxRate: 0.2,
    priceIncludesTax: false,
    decimalPlaces: 2
};

var taxConfigIncluding = {
    displayMode: 'including_tax',
    taxRate: 0.2,
    priceIncludesTax: false,
    decimalPlaces: 2
};

var taxConfigBoth = {
    displayMode: 'both_taxes',
    taxRate: 0.2,
    priceIncludesTax: false,
    decimalPlaces: 2
};

// Price Formatter Configuration
var priceFormatterConfig = {
    currencySymbol: '$',
    currencyPosition: 'before',
    decimalPlaces: 2,
    decimalSeparator: '.',
    thousandsSeparator: ','
};

// HOW TO USE IN BROWSER CONSOLE:
// ================================

// 1. Initialize the processor
// require(['HawkSearch_EsIndexing/js/pricing/PriceDataProcessor'], function(PriceDataProcessor) {
//     window.testProcessor = new PriceDataProcessor(priceFormatterConfig);
//     console.log('Processor initialized');
// });

// 2. Test with different products
// console.log('Simple with discount:', window.testProcessor.process(simpleProductWithDiscount, taxConfigExcluding));
// console.log('Bundle product:', window.testProcessor.process(bundleProduct, taxConfigBoth));
// console.log('Grouped product:', window.testProcessor.process(groupedProduct, taxConfigIncluding));

// 3. Test handler registry
// console.log('Supported types:', window.testProcessor.getSupportedTypes());
// console.log('Supports simple:', window.testProcessor.supportsProductType('simple'));
// console.log('Supports bundle:', window.testProcessor.supportsProductType('bundle'));

// EXPECTED OUTPUTS:
// ==================

// Simple Product with Discount (excluding_tax):
// {
//     type: 'simple',
//     uid: '123',
//     hasDiscount: true,
//     finalPrice: 32.00,
//     finalPriceFormatted: '$32.00',
//     regularPrice: 45.00,
//     regularPriceFormatted: '$45.00',
//     taxMode: 'excluding_tax',
//     priceRange: null,
//     discountPercent: 29,
//     discountAmount: 13.00
// }

// Bundle Product (both_taxes):
// {
//     type: 'bundle',
//     uid: '789',
//     hasDiscount: false,
//     finalPrice: 50.00,
//     finalPriceFormatted: '$50.00',
//     finalPriceIncludingTax: 60.00,
//     finalPriceIncludingTaxFormatted: '$60.00',
//     taxMode: 'both_taxes',
//     priceRange: {
//         minimum: {
//             amount: 50.00,
//             formatted: '$50.00',
//             type: 'minPrice'
//         },
//         maximum: {
//             amount: 150.00,
//             formatted: '$150.00',
//             type: 'maxPrice'
//         },
//         minimumIncludingTax: {
//             amount: 60.00,
//             formatted: '$60.00',
//             type: 'minPriceIncludingTax'
//         },
//         maximumIncludingTax: {
//             amount: 180.00,
//             formatted: '$180.00',
//             type: 'maxPriceIncludingTax'
//         },
//         hasRange: true,
//         rangeLabel: 'From'
//     }
// }

// MANUAL TESTING CHECKLIST:
// ==========================

// [ ] Test all product types (simple, virtual, downloadable, giftcard, bundle, grouped, configurable)
// [ ] Test all tax modes (excluding_tax, including_tax, both_taxes)
// [ ] Test discount scenarios (with/without discount)
// [ ] Test price ranges (bundle, configurable)
// [ ] Test starting price (grouped)
// [ ] Test edge cases (null prices, zero prices, negative prices)
// [ ] Test price formatting (different currencies, locales)
// [ ] Test error handling (missing fields, invalid data)
// [ ] Test handler registration (custom handlers)
// [ ] Test fallback handler (unknown product type)

// INTEGRATION TESTING:
// ====================

// Test with actual Vue component:
// 1. Load page with search results
// 2. Open browser console
// 3. Check rendered HTML structure
// 4. Verify CSS classes
// 5. Verify data attributes
// 6. Test responsive behavior
// 7. Test with different product types in results
// 8. Test tax mode switching
// 9. Compare with old component output
// 10. Verify no JavaScript errors

// Export for use in test files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        simpleProductWithDiscount: simpleProductWithDiscount,
        simpleProductNoDiscount: simpleProductNoDiscount,
        bundleProduct: bundleProduct,
        groupedProduct: groupedProduct,
        configurableProductWithRange: configurableProductWithRange,
        configurableProductDiscount: configurableProductDiscount,
        giftCardProduct: giftCardProduct,
        taxConfigExcluding: taxConfigExcluding,
        taxConfigIncluding: taxConfigIncluding,
        taxConfigBoth: taxConfigBoth,
        priceFormatterConfig: priceFormatterConfig
    };
}
