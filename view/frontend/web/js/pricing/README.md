# Pricing Module - Refactored Architecture

## Overview

This module implements a refactored pricing architecture that separates business logic from presentation, using product type handlers, a registry pattern, and hierarchical Vue components.

## Architecture

### Core Components

#### 1. Business Logic Layer

**PriceDataProcessor** (`pricing/PriceDataProcessor.js`)
- Main orchestrator for price data processing
- Initializes and manages ProductTypeHandlerRegistry
- Provides clean API: `process(productData, taxConfig)`

**ProductTypeHandlerRegistry** (`pricing/ProductTypeHandlerRegistry.js`)
- Manages registration and retrieval of product type handlers
- Supports fallback handler for unknown types
- Validates handler interfaces on registration

**Product Type Handlers** (`pricing/handlers/`)
- `BaseProductTypeHandler.js` - Abstract base class
- `SimpleProductHandler.js` - Simple, virtual, downloadable products
- `GiftCardProductHandler.js` - Gift card products
- `BundleProductHandler.js` - Bundle products with price ranges
- `GroupedProductHandler.js` - Grouped products with starting price
- `ConfigurableProductHandler.js` - Configurable products

**Utilities** (`pricing/utils/`)
- `TaxCalculator.js` - Tax calculation methods
- `PriceFormatter.js` - Currency formatting

#### 2. Presentation Layer

**Vue Components** (`vue-hawksearch/components/product-price/`)
- `product-price-refactored.js` - Main component (root)
- `price-range-wrapper.js` - Handles ranges and containers
- `price-container.js` - CSS class wrappers
- `price-label.js` - Label display with positioning
- `price-tax-wrapper.js` - Tax mode logic (transparent)
- `price-amount-wrapper.js` - Price value display (leaf)

### Data Flow

```
External Service Data (ProductTypeData)
    ↓
PriceDataProcessor.process()
    ↓
ProductTypeHandlerRegistry.getHandler()
    ↓
[Product Type Handler].process()
    ↓
Clean Business Data (PriceData)
    ↓
Vue Component (product-price-refactored)
    ↓
Hierarchical Sub-Components
    ↓
Rendered HTML
```

## Usage

### Basic Integration

```javascript
// In your Vue app or component
define([
    'HawkSearch_EsIndexing/js/vue-hawksearch/components/product-price-refactored'
], function(ProductPriceComponent) {
    // Component is automatically registered as 'product-price-refactored'
    
    // Use in template:
    // <product-price-refactored :document="productDoc" :pricing-config="config">
    // </product-price-refactored>
});
```

### Configuration

```javascript
window.hawksearchConfig = {
    pricing: {
        taxDisplayMode: 'excluding_tax', // or 'including_tax', 'both_taxes'
        mockTaxRate: 0.2, // 20% tax rate
        priceIncludesTax: false,
        priceFormat: {
            currencySymbol: '$',
            currencyPosition: 'before',
            decimalPlaces: 2,
            decimalSeparator: '.',
            thousandsSeparator: ','
        }
    }
};
```

### Product Data Structure

Input data from external service:
```javascript
{
    type_id: 'simple',           // Product type
    __uid: '123',                // Unique ID
    price_regular: 45.00,        // Regular price
    price_final: 32.00,          // Final price
    price_min: null,             // Min price (for ranges)
    price_max: null              // Max price (for ranges)
}
```

Processed output:
```javascript
{
    type: 'simple',
    uid: '123',
    hasDiscount: true,
    finalPrice: 32.00,
    finalPriceFormatted: '$32.00',
    regularPrice: 45.00,
    regularPriceFormatted: '$45.00',
    taxMode: 'excluding_tax',
    priceRange: null,
    // ... tax-inclusive prices if applicable
}
```

## Supported Product Types

- **Simple** - Basic products with optional discounts
- **Virtual** - Same as simple (uses SimpleProductHandler)
- **Downloadable** - Same as simple (uses SimpleProductHandler)
- **Giftcard** - Fixed or open amount gift cards
- **Bundle** - Products with price ranges (min/max)
- **Grouped** - Products showing starting price
- **Configurable** - Products with optional price ranges

## Tax Display Modes

- **excluding_tax** - Show prices without tax
- **including_tax** - Show prices with tax included
- **both_taxes** - Show both excluding and including tax prices

## Component Hierarchy

```
product-price-refactored (root)
  └─ price-range-wrapper
      └─ price-container
          └─ price-label
              └─ price-tax-wrapper
                  └─ price-amount-wrapper (leaf)
```

## Benefits of Refactored Architecture

1. **Separation of Concerns**
   - Business logic in handlers
   - Presentation logic in Vue components
   - No template selection in business layer

2. **Maintainability**
   - Single Responsibility Principle
   - Easy to modify individual handlers
   - Clear component boundaries

3. **Testability**
   - Handlers testable in isolation
   - Components testable independently
   - Mock-friendly architecture

4. **Extensibility**
   - Easy to add new product types
   - New handlers register without modifying existing code
   - Components reusable across product types

## Migration Path

The refactored component is created as `product-price-refactored` to allow:
1. Parallel deployment with old component
2. Gradual rollout and testing
3. A/B testing capabilities
4. Safe rollback if issues discovered

To migrate:
1. Test `product-price-refactored` in development
2. Compare output with original component
3. Enable for subset of users
4. Monitor for issues
5. Full cutover when validated
6. Remove old component code

## File Structure

```
view/frontend/web/
├── js/
│   ├── pricing/
│   │   ├── PriceDataProcessor.js
│   │   ├── ProductTypeHandlerRegistry.js
│   │   ├── handlers/
│   │   │   ├── BaseProductTypeHandler.js
│   │   │   ├── SimpleProductHandler.js
│   │   │   ├── GiftCardProductHandler.js
│   │   │   ├── BundleProductHandler.js
│   │   │   ├── GroupedProductHandler.js
│   │   │   └── ConfigurableProductHandler.js
│   │   └── utils/
│   │       ├── TaxCalculator.js
│   │       └── PriceFormatter.js
│   └── vue-hawksearch/
│       └── components/
│           ├── product-price-refactored.js
│           └── product-price/
│               ├── price-amount-wrapper.js
│               ├── price-tax-wrapper.js
│               ├── price-label.js
│               ├── price-container.js
│               └── price-range-wrapper.js
└── template/
    └── vue-hawksearch/
        └── components/
            ├── product-price-refactored.html
            └── product-price/
                ├── price-amount-wrapper.html
                ├── price-tax-wrapper.html
                ├── price-label.html
                ├── price-container.html
                └── price-range-wrapper.html
```

## Development

### Adding a New Product Type Handler

1. Create handler class extending `BaseProductTypeHandler`
2. Implement `canHandle(productType)` method
3. Implement `process(productData, taxConfig)` method
4. Register in `PriceDataProcessor._initializeHandlers()`

Example:
```javascript
define([
    'HawkSearch_EsIndexing/js/pricing/handlers/BaseProductTypeHandler'
], function(BaseProductTypeHandler) {
    function MyProductHandler() {
        BaseProductTypeHandler.call(this);
    }
    
    MyProductHandler.prototype = Object.create(BaseProductTypeHandler.prototype);
    
    MyProductHandler.prototype.canHandle = function(productType) {
        return productType === 'mytype';
    };
    
    MyProductHandler.prototype.process = function(productData, taxConfig) {
        // Implementation
    };
    
    return MyProductHandler;
});
```

### Debugging

Enable debug logging:
```javascript
window.hawksearchConfig.pricing.debug = true;
```

Common issues:
- Missing `hawksearch` SDK: Check RequireJS configuration
- Invalid product data: Check handler validation
- Formatting issues: Verify `priceFormat` configuration

## Performance

- Handlers: O(1) lookup via registry
- Processing: Single pass through data
- Components: Minimal re-rendering with proper props
- No watchers on large objects

## Browser Support

- Modern browsers (ES5+)
- RequireJS compatible
- Vue.js 2.x compatible
