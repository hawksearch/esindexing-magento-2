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

// Configurable Products
// **Example 1: Configurable Product with Price Range (with discount)**
/*
-   Given:
    `price_regular = 70`,
    `price_final = 60`,
    `price_min = 60`,
    `price_max = 70`

-   **Calculation**:

    ```
    samePriceForAll = false (60 !== 70)
    discount_multiplier = 60 / 70 = 0.857
    price_min_regular = 60 / 0.857 = 70
    ```
**Expected Display**:
    As low as  **60$**  (Regular: 70$)
 */


/*
**Example 2: Configurable Product with Same Price for All Variations (with discount)**

-   Given:
    `price_regular = 80`,
    `price_final = 70`,
    `price_min = 70`,
    `price_max = 70`

-   **Calculation**:

    ```
    samePriceForAll = true (70 === 70)
    discount_multiplier = 70 / 80 = 0.875
    price_min_regular = 70 / 0.875 = 80
    ```

-   **Expected Display**:
    **70$**  (Regular: 80$)
 */

/*
**Example 3: Configurable Product with Price Range (no discount)**

-   Given:
    `price_regular = 60`,
    `price_final = 60`,
    `price_min = 60`,
    `price_max = 70`

-   **Calculation**:

    -   Condition not met (`price_regular === price_final`), so we cannot calculate different regular prices
    -   Use fallback display format
-   **Expected Display**:
    As low as  **60$**
 */
var configurableProductWithRangeNoDiscount = {
    type_id: 'configurable',
    __uid: '202',
    price_regular: 0,
    price_final: 0,
    price_min: 40.00,
    price_max: 52.00
};
/*
**Example 4: Configurable Product with Same Price (no discount)**

-   Given:
    `price_regular = 0`,
    `price_final = 0`,
    `price_min = 70`,
    `price_max = 70`

-   **Calculation**:

    -   `samePriceForAll = true`
    -   Condition not met (`price_regular === price_final`), so no separate regular price needed
    -   Use fallback display format
-   **Expected Display**:
    **70$**
 */
var configurableProductSamePriceNoDiscount = {
    type_id: 'configurable',
    __uid: '202',
    price_regular: 0,
    price_final: 0,
    price_min: 63.00,
    price_max: 63.00
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
//     discount: {hasDiscount: true, discountRate: 0.28888888888888886,
//     finalPrice: {amount: 32, formatted: '$32'},
//     finalPriceIncludingTax: {amount: 38.4, formatted: '$38.40'},
//     regularPrice: {amount: 45, formatted: '$45.00'},
//     regularPriceIncludingTax: {amount: 54, formatted: '$54.00'},
//     taxMode: 'excluding_tax',
//     priceRange: [],
// }

// Bundle Product (both_taxes):
// {
//     type: 'bundle',
//     uid: '789',
//     discount: {hasDiscount: false, discountRate: 0,
//     finalPrice: {amount: 50, formatted: '$50.00'},
//     finalPriceIncludingTax: {amount: 60, formatted: '$60.00'},
//     regularPrice: null,
//     regularPriceIncludingTax: null,
//     taxMode: 'both_taxes',
//     priceRange: [
//         {
//             "amount": {
//                 "amount": 50,
//                 "formatted": "$50.00"
//             },
//             "amountIncludingTax": {
//                 "amount": 60,
//                 "formatted": "$60.00"
//             },
//             "regularAmount": {
//                 "amount": 50,
//                 "formatted": "$50.00"
//             },
//             "regularAmountIncludingTax": {
//                 "amount": 60,
//                 "formatted": "$60.00"
//             },
//             "rangeItemType": "from"
//          },
//         {
//             "amount": {
//                 "amount": 150,
//                 "formatted": "$150.00"
//             },
//             "amountIncludingTax": {
//                 "amount": 180,
//                 "formatted": "$180.00"
//             },
//             "regularAmount": {
//                 "amount": 150,
//                 "formatted": "$150.00"
//             },
//             "regularAmountIncludingTax": {
//                 "amount": 180,
//                 "formatted": "$180.00"
//             },
//             "rangeItemType": "to"
//         }
//     ]
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
