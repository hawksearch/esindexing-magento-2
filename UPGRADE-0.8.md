# UPGRADE FROM 0.7 to 0.8

## Overview

Version 0.8.0 is a major update focused on code quality, type safety, and performance improvements. The main changes include:

- **Minimum Requirements**: PHP 8.1+ and Magento 2.4.4+ are now required
- **Type Safety**: Comprehensive type hints added to method arguments, return types, and properties
- **API Contracts**: Public API interfaces and classes are now formally declared with `@api` annotations
- **Performance**: Major indexing performance improvements including price indexing optimization and caching
- **Code Quality**: Protected properties visibility changed to private with proper deprecation notices
- **Complex Products**: Enhanced support for displaying prices of configurable, bundle, and grouped products

This release follows the [Backward Compatibility Policy](https://developerdocs.hawksearch.com/docs/magento-developers-bc-policy). All breaking changes are properly deprecated with clear migration paths.

## Table of Contents

- [System Requirements](#system-requirements)
- [Type Hints and Strict Typing](#type-hints-and-strict-typing)
- [API Contracts Declaration](#api-contracts-declaration)
- [Deprecated Protected Properties](#deprecated-protected-properties)
- [Deprecated Pricing Methods](#deprecated-pricing-methods)
- [Deprecated JavaScript Functions](#deprecated-javascript-functions)
- [New Features](#new-features)
- [Performance Improvements](#performance-improvements)
- [Migration Examples](#migration-examples)

## System Requirements

### Minimum Version Requirements

*Before (0.7.x):*
- PHP 7.4+
- Magento 2.4.3+

*After (0.8.0):*
- **PHP 8.1+** (required)
- **Magento 2.4.4+** (required)

**Action Required:** Upgrade your environment to meet the minimum requirements before upgrading to version 0.8.0.

## Type Hints and Strict Typing

Version 0.8.0 introduces comprehensive type hints across the codebase, improving code reliability and IDE support.

### Method Argument Type Hints

All public API methods now have proper type hints for arguments:

*Before (0.7.x):*
```php
interface SearchRequestInterface
{
    public function setIndexName($value);
    public function setPageNo($value);
    public function setMaxPerPage($value);
}
```

*After (0.8.0):*
```php
interface SearchRequestInterface
{
    public function setIndexName(?string $value): self;
    public function setPageNo(int $value): self;
    public function setMaxPerPage(int $value): self;
}
```

### Return Type Declarations

All methods now declare return types:

*Before (0.7.x):*
```php
class SearchRequest
{
    public function getIndexName()
    {
        return (string)$this->_get(self::FIELD_INDEX_NAME);
    }
}
```

*After (0.8.0):*
```php
class SearchRequest
{
    public function getIndexName(): string
    {
        return (string)$this->_get(self::FIELD_INDEX_NAME);
    }
}
```

### Property Type Declarations

All private, final, and internal class properties now have type declarations:

*Before (0.7.x):*
```php
class SearchRequest
{
    private $clientDataFactory;
    private $variantOptionsFactory;
    private $smartBarFactory;
}
```

*After (0.8.0):*
```php
class SearchRequest
{
    private ClientDataInterfaceFactory $clientDataFactory;
    private VariantOptionsInterfaceFactory $variantOptionsFactory;
    private SmartBarInterfaceFactory $smartBarFactory;
}
```

**Action Required:** 
- Review custom implementations of interfaces to ensure they match the new type signatures
- Update any code that extends framework classes to include proper type declarations
- Enable strict type checking in your custom modules: `declare(strict_types=1);`

## API Contracts Declaration

### Public API Interfaces

The following interfaces in `\HawkSearch\EsIndexing` namespace are now defined as `@api` and follow backward compatibility policy:

**Data Interfaces:**
- Api\Data\BoostQueryInterface
- Api\Data\ClientDataInterface
- Api\Data\CoordinateInterface
- Api\Data\EsIndexInterface
- Api\Data\FacetBoostBuryInterface
- Api\Data\FacetInterface
- Api\Data\FacetRangeModelInterface
- Api\Data\FacetValueOrderInfoInterface
- Api\Data\FieldInterface
- Api\Data\HierarchyInterface
- Api\Data\IndexListInterface
- Api\Data\LandingPageInterface
- Api\Data\QueueOperationDataInterface
- Api\Data\SearchRequestInterface
- Api\Data\SmartBarInterface
- Api\Data\VariantOptionsInterface

**Management Interfaces:**
- Api\FacetManagementInterface
- Api\FieldManagementInterface
- Api\HierarchyManagementInterface
- Api\IndexManagementInterface
- Api\LandingPageManagementInterface

**Model Interfaces:**
- Model\Indexer\Entities\SchedulerInterface
- Model\Indexing\ContextInterface
- Model\Indexing\FieldHandlerInterface
- Model\Indexing\EntityRebuildInterface
- Model\Indexing\EntityTypeInterface
- Model\Indexing\Field\NameProviderInterface
- Model\Indexing\ItemsDataProviderInterface
- Model\Indexing\ItemsIndexerInterface
- Model\Layout\LayoutConfigProcessorInterface
- Model\MessageQueue\Validator\OperationValidatorInterface
- Model\MessageQueue\BulkPublisherInterface
- Model\MessageQueue\MessageManagerInterface
- Model\MessageQueue\MessageTopicResolverInterface
- Model\Product\Attribute\ExcludeNotVisibleProductsFlagInterface
- Model\Product\PriceManagementInterface
- Model\Product\ProductTypeInterface
- Model\Product\ProductTypePoolInterface
- Service\DataStorageInterface

### Internal Interfaces

The following interfaces are marked as `@internal` (experimental features, subject to change):
- Api\Data\IndexItemInterface
- Api\Data\IndexItemsContextInterface
- Model\Config\Backend\Serialized\Processor\ValueProcessorInterface
- Model\Field\FieldExtendedInterface

### Public API Classes

The following classes in `\HawkSearch\EsIndexing` namespace are defined as `@api`:

**Block Classes:**
- Block\Adminhtml\Bulk\Details\BackButton
- Block\Adminhtml\Bulk\Details\RetryButton
- Block\Adminhtml\Form\GenericButton
- Block\Tracking

**Service Classes:**
- Registry\CurrentCategory
- Service\DataStorage

**Model Classes:**
- Model\BulkOperation\BulkOperationManagement
- Model\Indexer\Entities\ActionAbstract
- Model\Indexer\Entities\SchedulerAbstract
- Model\Indexer\Entities\SchedulerComposite
- Model\Indexing\AbstractConfigHelper
- Model\Indexing\AbstractEntityRebuild
- Model\Indexing\Field\DefaultNameProvider
- Model\Indexing\FieldHandler\Composite
- Model\Indexing\FieldHandler\DataObjectHandler
- Model\Layout\CompositeConfigProcessor
- Model\MessageQueue\Consumer
- Model\MessageQueue\Exception\InvalidBulkOperationException
- Model\MessageQueue\MessageTopicByObjectResolver
- Model\MessageQueue\QueueOperationData
- Model\FacetManagement
- Model\FieldManagement
- Model\HierarchyManagement
- Model\IndexManagement
- Model\LandingPageManagement
- Model\Product
- Model\Product\Attributes
- Model\Product\PriceManagement
- Model\Product\ProductType\CompositeType
- Model\Product\ProductType\DefaultType
- Model\Product\ProductTypePool

**Action Required:** 
- Use only `@api` marked interfaces and classes in your customizations
- Avoid depending on `@internal` interfaces as they may change without notice
- Review any custom code that implements or extends API classes

## Deprecated Protected Properties

To improve encapsulation and follow best practices, several protected properties have been deprecated and changed to private. Access to these properties should now be done through constructor injection.

### Affected Classes and Properties

#### `Block\Adminhtml\System\Config\Product\CustomAttributes`

*Deprecated Properties:*
- `columnRendererCache` - Changed to private. Can't be extended with DI

*Deprecated Methods:*
- `getColumnRenderer()` - Deprecated since 0.8.0. Use after plugin for `addColumn()` method instead
- `resolveSelectFieldRenderer()` - Deprecated since 0.8.0. Use after plugin for `addColumn()` method instead

**Reason:** These methods are not designed to be overridden. The class is being closed for extension to prevent design flaws and provide well-defined extension points through plugins.

*Before (0.7.x):*
```php
class MyCustomAttributes extends CustomAttributes
{
    protected function getColumnRenderer(string $columnName)
    {
        // Custom renderer logic
        $renderer = parent::getColumnRenderer($columnName);
        // Customize renderer
        return $renderer;
    }
    
    protected function resolveSelectFieldRenderer(string $columnName)
    {
        // Custom select field renderer
        return parent::resolveSelectFieldRenderer($columnName);
    }
}
```

*After (0.8.0):*
```php
// Use after plugin for addColumn() method
class CustomAttributesPlugin
{
    /**
     * Customize column renderer after it's added
     */
    public function afterAddColumn(
        CustomAttributes $subject,
        $result,
        string $name,
        array $params
    ) {
        // Your customization logic here
        // Modify columns through $subject->getColumns()
        return $result;
    }
}
```

```xml
<!-- etc/adminhtml/di.xml -->
<type name="HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes">
    <plugin name="vendor_module_custom_attributes" 
            type="Vendor\Module\Plugin\CustomAttributesPlugin" 
            sortOrder="10"/>
</type>
```


#### `Model\Indexer\Entities\ActionAbstract`

*Deprecated Properties:*
- `eventManager` - Set via constructor injection
- `messageManager` - Set via constructor injection
- `publisher` - Set via constructor injection
- `entityScheduler` - Set via constructor injection

*Before (0.7.x):*
```php
class MyIndexerAction extends ActionAbstract
{
    protected function doSomething()
    {
        // Direct property access
        $this->eventManager->dispatch('my_event', ['data' => $data]);
        $this->messageManager->addErrorMessage('Error occurred');
    }
}
```

*After (0.8.0):*
```php
class MyIndexerAction extends ActionAbstract
{
    private ManagerInterface $myEventManager;
    private MessageManagerInterface $myMessageManager;

    public function __construct(
        ManagerInterface $eventManager,
        MessageManagerInterface $messageManager,
        BulkPublisherInterface $publisher,
        SchedulerInterface $entityScheduler,
        ManagerInterface $myEventManager,
        MessageManagerInterface $myMessageManager
    ) {
        parent::__construct($eventManager, $messageManager, $publisher, $entityScheduler);
        $this->myEventManager = $myEventManager;
        $this->myMessageManager = $myMessageManager;
    }

    protected function doSomething()
    {
        // Use injected property
        $this->myEventManager->dispatch('my_event', ['data' => $data]);
        $this->myMessageManager->addErrorMessage('Error occurred');
    }
}
```

#### `Model\Indexer\Entities\SchedulerComposite`

*Deprecated Property:*
- `schedulers` - Set via constructor injection

*Before (0.7.x):*
```php
class MyScheduler extends SchedulerComposite
{
    protected function getSchedulers()
    {
        return $this->schedulers;
    }
}
```

*After (0.8.0):*
```php
class MyScheduler extends SchedulerComposite
{
    private TMap $mySchedulers;

    public function __construct(
        TMapFactory $tmapFactory,
        array $schedulers = []
    ) {
        parent::__construct($tmapFactory, $schedulers);
        $this->mySchedulers = $tmapFactory->create([
            'array' => $schedulers,
            'type' => SchedulerInterface::class
        ]);
    }
}
```

#### `Model\Indexing\AbstractEntityRebuild`

*Deprecated Properties:*
- `entityTypePool` - Set via constructor injection
- `eventManager` - Set via constructor injection
- `hawkLogger` - Set via `$loggerFactory` constructor injection
- `storeManager` - Set via constructor injection
- `indexingContext` - Set via constructor injection

*Before (0.7.x):*
```php
class MyEntityRebuild extends AbstractEntityRebuild
{
    protected function rebuildStoreIndex(StoreInterface $store, array $productIds = null)
    {
        // Direct property access
        $this->hawkLogger->info('Starting rebuild');
        $this->eventManager->dispatch('before_rebuild', []);
        
        return parent::rebuildStoreIndex($store, $productIds);
    }
}
```

*After (0.8.0):*
```php
class MyEntityRebuild extends AbstractEntityRebuild
{
    private LoggerInterface $myLogger;
    private ManagerInterface $myEventManager;

    public function __construct(
        EntityTypePoolInterface $entityTypePool,
        ManagerInterface $eventManager,
        LoggerFactoryInterface $loggerFactory,
        StoreManagerInterface $storeManager,
        ContextInterface $indexingContext,
        LoggerInterface $myLogger,
        ManagerInterface $myEventManager
    ) {
        parent::__construct(
            $entityTypePool,
            $eventManager,
            $loggerFactory,
            $storeManager,
            $indexingContext
        );
        $this->myLogger = $myLogger;
        $this->myEventManager = $myEventManager;
    }

    protected function rebuildStoreIndex(StoreInterface $store, array $productIds = null)
    {
        // Use injected properties
        $this->myLogger->info('Starting rebuild');
        $this->myEventManager->dispatch('before_rebuild', []);
        
        return parent::rebuildStoreIndex($store, $productIds);
    }
}
```

#### `Model\Indexing\FieldHandler\Composite`

*Deprecated Property:*
- `handlers` - Set via constructor injection

*Before (0.7.x):*
```php
class MyCompositeHandler extends Composite
{
    protected function getHandlers()
    {
        return $this->handlers;
    }
}
```

*After (0.8.0):*
```php
class MyCompositeHandler extends Composite
{
    private array $myHandlers;

    public function __construct(array $handlers = [])
    {
        parent::__construct($handlers);
        $this->myHandlers = $handlers;
    }
}
```

**Action Required:**
- Review all custom classes extending the affected classes
- Replace direct property access with constructor injection
- Replace method overrides with plugin-based approach where applicable
- Deprecation warnings will be triggered in 0.8.0 but code will still work
- Protected properties and methods will become truly private in version 1.0.0

## Deprecated Pricing Methods

Version 0.8.0 introduces major changes to price handling. Prices are now retrieved from Magento's native price index instead of being calculated during indexing. All tax and formatting logic has been moved to the frontend Vue component.

### Backend Price Methods (Model\Product\ProductType\DefaultType)

The following methods in product type classes are deprecated as prices now come from Magento's price index:

**Deprecated Methods:**

- `getMinMaxPrice()` - **Deprecated since 0.8.0**. Prices are now retrieved from Magento's price index.
- `addPricesIncludingTax()` - **Deprecated since 0.8.0**. Tax calculations should be based on shipping address in the UI layer.
- `addFormattedPrices()` - **Deprecated since 0.8.0**. Price formatting should be applied in the UI.
- `handleTax()` - **Deprecated since 0.8.0**. Tax handling moved to frontend.
- `getTierPrices()` - **Deprecated since 0.8.0**. Tier prices are retrieved from price index.
- `getAllCustomerGroupsId()` - **Deprecated since 0.8.0**. Customer group handling is in price index.

**Reason:** 
- Magento already has pre-calculated prices in its `catalog_product_index_price` table
- Avoids redundant price calculations during indexing
- Tax should be calculated based on customer's shipping address (frontend)
- Price formatting is UI concern and varies by locale

*Before (0.7.x):*
```php
class CustomProductType extends DefaultType
{
    public function getPriceData(Product $product, array $priceData = []): array
    {
        // Calculate min/max prices
        $minMaxPrices = $this->getMinMaxPrice($product);
        $priceData['price_min'] = $minMaxPrices['min'];
        $priceData['price_max'] = $minMaxPrices['max'];
        
        // Add prices including tax
        $priceData = $this->addPricesIncludingTax($product, $priceData);
        
        // Add formatted prices
        $priceData = $this->addFormattedPrices($priceData);
        
        // Get tier prices
        $priceData['tier_prices'] = $this->getTierPrices($product);
        
        return $priceData;
    }
}
```

*After (0.8.0):*
```php
class CustomProductType extends DefaultType
{
    public function getPriceData(Product $product, array $priceData = []): array
    {
        // Prices are automatically retrieved from Magento's price index
        // via PriceManagement service
        // No need to calculate min/max, tax, or format prices
        
        // Just return the base data, price index handles the rest
        return parent::getPriceData($product, $priceData);
    }
}
```

### Backend Price Template Generation (Model\Layout\ConfigProcessor\JsGlobal\PricingConfigProcessor)

**Deprecated:** Backend price template and HTML generation in `PricingConfigProcessor::process()` method.

*Before (0.7.x):*
```php
// PricingConfigProcessor generated HTML templates for prices
public function process(array $jsConfig): array
{
    $priceTemplates = [];
    // ... complex HTML template generation
    $jsConfig['pricing']['priceTemplates'] = $priceTemplates;
    $jsConfig['pricing']['patterns'] = $patterns;
    return $jsConfig;
}
```

*After (0.8.0):*
```php
// Only raw price data and config are provided
public function process(array $jsConfig): array
{
    $jsConfig['pricing'] = [
        'currencyCode' => $this->storeManager->getStore()->getCurrentCurrencyCode(),
        'priceFormat' => $this->localeFormat->getPriceFormat(),
        'currencyFormat' => $this->storeManager->getStore()->getCurrentCurrency()->getOutputFormat(),
        // No patterns or priceTemplates provided anymore
    ];
    return $jsConfig;
}
```

**Impact:** All price rendering is now handled by the Vue component on the frontend.

## Deprecated JavaScript Functions

Starting with version 0.8.0, legacy JavaScript price rendering logic is deprecated:

### Frontend Price Rendering (view/frontend/web/js/common.js)

**Deprecated Function:**
- `hawksearch.formatItemPrice()` - **Deprecated since 0.8.0**. Now returns empty string as no-op.

**Reason:** All price display is now handled by the Vue `product-price` component.

*Before (0.7.x):*
```javascript
// Legacy price formatting in JavaScript
formatItemPrice: function(document) {
    const templateType = (_.indexOf(
        _.keys(hawksearchConfig.pricing.priceTemplates),
        this.getDocumentField(document, "type_id")
    ) !== -1) ? 
        this.getDocumentField(document, "type_id") : 'default';
    
    const priceTemplate = hawksearchConfig.pricing.priceTemplates[templateType]['regular'];
    
    return mageTemplate.template(priceTemplate, {
        uid: this.extractId(document),
        price_regular: this.getDocumentField(document, "price_regular"),
        price_final: this.getDocumentField(document, "price_final"),
        // ... more price fields
    });
}
```

*After (0.8.0):*
```javascript
// Deprecated no-op function
formatItemPrice: function() {
    // Deprecated: do not use. All price rendering is handled by the Vue component.
    return '';
}
```

**Migration:** Remove any calls to `hawksearch.formatItemPrice()` and use the Vue `<product-price>` component instead:

*Before (0.7.x):*
```html
<span v-html="hawksearch.formatItemPrice(result.Document || {})"/>
```

*After (0.8.0):*
```html
<product-price></product-price>
```

### Deprecated JavaScript Configurations

**Deprecated Configurations:**
- `window.hawksearchConfig.patterns` - **Removed in 0.8.0**
- `window.hawksearchConfig.priceTemplates` - **Removed in 0.8.0**

**Reason:** Price templates and patterns are no longer generated server-side. All price rendering happens in the Vue component.

**Action Required:**
- Remove any custom code that reads `hawksearchConfig.patterns` or `hawksearchConfig.priceTemplates`
- Use the Vue `product-price` component for all price display
- Access raw price data from product documents via `result.Document.price_*` fields
- Apply formatting using `hawksearchConfig.pricing.priceFormat` if needed

## New Features

### Price Indexing Configuration

Version 0.8.0 introduces the ability to enable/disable price indexing via configuration:

```xml
<!-- app/code/Vendor/Module/etc/config.xml -->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Store:etc/config.xsd">
    <default>
        <hawksearch_esindexing>
            <products>
                <enable_price_indexing>1</enable_price_indexing>
            </products>
        </hawksearch_esindexing>
    </default>
</config>
```

**Configuration Path:** Stores > Configuration > Hawksearch > Products > Enable Price Indexing

This allows you to control whether product prices are indexed to Hawksearch, which can be useful for:
- Separating price data from product data
- Improving indexing performance when prices are not needed in search
- Compliance requirements that separate pricing data

### Complex Product Price Display

Enhanced price display for configurable, bundle, and grouped products in search results:

**Price Label Formatting:**
- **Simple products:** `$###`
- **Configurable products:** `As low as: $###`
- **Bundle products:** `From: $### To: $###`
- **Grouped products:** `Starting at: $###`

Prices now correctly reflect:
- Customer group pricing
- Catalog price rules
- Special prices
- Tier prices

### Price Filtered Field

A new `price_filtered` field has been added to the product index for optimal filtering and sorting:

**Benefits:**
- Improved faceting performance for price range filters
- Better sorting capabilities by price
- Optimized for Hawksearch's filtering engine

**Field Details:**
```php
// The price_filtered field contains the final customer-facing price
// Used for:
// - Price range facets
// - Sorting by price
// - Price-based search queries
[
    'price_filtered' => 29.99,  // Final price for filtering/sorting
    'price_regular' => 39.99,    // Regular price
    'price_final' => 29.99,      // Final price (may include discounts)
]
```

**Configuration:**
```xml
<!-- Enable/disable price indexing -->
<config>
    <default>
        <hawksearch_esindexing>
            <products>
                <enable_price_indexing>1</enable_price_indexing>
            </products>
        </hawksearch_esindexing>
    </default>
</config>
```

### Index Data Consistency

Improved index management ensures consistency between local database and Hawksearch API:

**Automatic Cleanup:**
- Stale index request data is automatically cleaned up
- Failed operations are tracked and can be retried
- Index switching happens automatically after successful full reindex

### SearchRequest Interface

A new `SearchRequestInterface` has been introduced with complete type safety:

```php
use HawkSearch\EsIndexing\Api\Data\SearchRequestInterface;
use HawkSearch\EsIndexing\Api\Data\SearchRequestInterfaceFactory;

class MySearchService
{
    private SearchRequestInterfaceFactory $searchRequestFactory;
    
    public function __construct(SearchRequestInterfaceFactory $searchRequestFactory)
    {
        $this->searchRequestFactory = $searchRequestFactory;
    }
    
    public function search(string $query, int $page = 1): SearchRequestInterface
    {
        $searchRequest = $this->searchRequestFactory->create();
        $searchRequest->setKeyword($query);
        $searchRequest->setPageNo($page);
        $searchRequest->setMaxPerPage(20);
        
        return $searchRequest;
    }
}
```

## Performance Improvements

### Price Index Optimization

Version 0.8.0 uses Magento's native price index for better performance:

*Before (0.7.x):*
- Prices calculated on-the-fly during indexing
- Separate queries for each product's price

*After (0.8.0):*
- Prices retrieved from Magento's price index table
- Batch processing for better performance
- Significant reduction in database queries

### Configuration Caching

*New caching mechanisms:*
- Field mapping configuration cache
- Attribute source cache
- Reduced configuration unserialize operations

### Middleware Table for API Requests

Product data is now pushed through a middleware table for better reliability:

*Benefits:*
- Retry failed operations automatically
- Track indexing progress
- Handle large datasets more efficiently

## Migration Examples

### Example 1: Migrating from Deprecated Protected Methods to Plugins

The `CustomAttributes` class methods `getColumnRenderer()` and `resolveSelectFieldRenderer()` are deprecated. Use plugins instead of extending the class.

*Before (0.7.x):*
```php
<?php
namespace Vendor\Module\Block\Adminhtml\System\Config\Product;

use HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes;

class MyCustomAttributes extends CustomAttributes
{
    /**
     * Override to add custom column renderer
     */
    protected function getColumnRenderer(string $columnName)
    {
        $renderer = parent::getColumnRenderer($columnName);
        
        // Custom logic to modify renderer
        if ($columnName === 'my_custom_field') {
            // Apply custom renderer settings
            $renderer->setSomeCustomOption('value');
        }
        
        return $renderer;
    }
    
    /**
     * Override to customize select field renderer
     */
    protected function resolveSelectFieldRenderer(string $columnName)
    {
        if ($columnName === 'my_custom_field') {
            // Custom renderer for specific field
            return $this->getMyCustomRenderer();
        }
        
        return parent::resolveSelectFieldRenderer($columnName);
    }
}
```

*After (0.8.0):*
```php
<?php
namespace Vendor\Module\Plugin\Block\Adminhtml\System\Config\Product;

use HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes;

class CustomAttributesPlugin
{
    /**
     * Plugin to customize columns after they are added
     *
     * @param CustomAttributes $subject
     * @param mixed $result
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function afterAddColumn(
        CustomAttributes $subject,
        $result,
        string $name,
        array $params
    ) {
        // Access columns and modify as needed
        $columns = $subject->getColumns();
        
        if ($name === 'my_custom_field' && isset($columns[$name])) {
            // Customize the renderer or other column properties
            // Note: Renderer is already created, access it via columns
            $renderer = $columns[$name]['renderer'] ?? null;
            if ($renderer) {
                $renderer->setSomeCustomOption('value');
            }
        }
        
        return $result;
    }
}
```

```xml
<!-- etc/adminhtml/di.xml -->
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="HawkSearch\EsIndexing\Block\Adminhtml\System\Config\Product\CustomAttributes">
        <plugin name="vendor_module_custom_attributes" 
                type="Vendor\Module\Plugin\Block\Adminhtml\System\Config\Product\CustomAttributesPlugin" 
                sortOrder="10"/>
    </type>
</config>
```

### Example 2: Custom Indexer Action with Property Injection

*Before (0.7.x):*
```php
<?php
namespace Vendor\Module\Model\Indexer;

use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract;

class CustomAction extends ActionAbstract
{
    public function executeFull()
    {
        // Access protected property directly
        $this->eventManager->dispatch('custom_index_before');
        
        // Your indexing logic
        $result = $this->performIndexing();
        
        if ($result) {
            $this->messageManager->addSuccessMessage(__('Indexing completed'));
        }
        
        return $this;
    }
    
    protected function performIndexing()
    {
        // Indexing logic
        return true;
    }
}
```

*After (0.8.0):*
```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Model\Indexer;

use HawkSearch\EsIndexing\Model\Indexer\Entities\ActionAbstract;
use HawkSearch\EsIndexing\Model\Indexer\Entities\SchedulerInterface;
use HawkSearch\EsIndexing\Model\MessageQueue\BulkPublisherInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class CustomAction extends ActionAbstract
{
    private ManagerInterface $customEventManager;
    private MessageManagerInterface $customMessageManager;

    public function __construct(
        ManagerInterface $eventManager,
        MessageManagerInterface $messageManager,
        BulkPublisherInterface $publisher,
        SchedulerInterface $entityScheduler,
        ManagerInterface $customEventManager,
        MessageManagerInterface $customMessageManager
    ) {
        parent::__construct($eventManager, $messageManager, $publisher, $entityScheduler);
        $this->customEventManager = $customEventManager;
        $this->customMessageManager = $customMessageManager;
    }

    public function executeFull(): void
    {
        // Use injected dependency
        $this->customEventManager->dispatch('custom_index_before');
        
        // Your indexing logic
        $result = $this->performIndexing();
        
        if ($result) {
            $this->customMessageManager->addSuccessMessage(__('Indexing completed'));
        }
    }
    
    private function performIndexing(): bool
    {
        // Indexing logic with type hints
        return true;
    }
}
```

### Example 3: Custom Field Handler with Type Hints

*Before (0.7.x):*
```php
<?php
namespace Vendor\Module\Model\Product\Attribute\Handler;

use HawkSearch\EsIndexing\Model\Product\Attribute\Handler\DefaultHandler;

class CustomAttribute extends DefaultHandler
{
    public function handle($item, $attributeCode)
    {
        // Get base value without type hints
        $value = parent::handle($item, $attributeCode);
        
        // Custom processing
        if ($attributeCode === 'custom_field') {
            $value = $this->processCustomField($value);
        }
        
        return $value;
    }
    
    protected function processCustomField($value)
    {
        // Transform value
        return strtoupper($value);
    }
}
```

*After (0.8.0):*
```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Model\Product\Field\Handler;

use HawkSearch\EsIndexing\Model\Product\Field\Handler\DefaultHandler;
use Magento\Framework\DataObject;

class CustomField extends DefaultHandler
{
    public function handle(DataObject $item, string $fieldName): mixed
    {
        // Get base value with proper type hints
        $value = parent::handle($item, $fieldName);
        
        // Custom processing with type safety
        if ($fieldName === 'custom_field') {
            $value = $this->processCustomField($value);
        }
        
        return $value;
    }
    
    private function processCustomField(mixed $value): string
    {
        // Transform value with type hints
        return strtoupper((string)$value);
    }
}
```

### Example 4: Implementing SearchRequestInterface with Proper Types

*Before (0.7.x):*
```php
<?php
namespace Vendor\Module\Model;

use HawkSearch\EsIndexing\Api\Data\SearchRequestInterface;

class CustomSearchRequest implements SearchRequestInterface
{
    private $keyword;
    private $pageNo;
    private $maxPerPage;
    
    public function setKeyword($value)
    {
        $this->keyword = $value;
        return $this;
    }
    
    public function getKeyword()
    {
        return $this->keyword;
    }
    
    public function setPageNo($value)
    {
        $this->pageNo = $value;
        return $this;
    }
    
    public function getPageNo()
    {
        return $this->pageNo;
    }
    
    // ... other methods without type hints
}
```

*After (0.8.0):*
```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Model;

use HawkSearch\EsIndexing\Api\Data\SearchRequestInterface;

class CustomSearchRequest implements SearchRequestInterface
{
    private string $keyword = '';
    private int $pageNo = 1;
    private int $maxPerPage = 20;
    
    public function setKeyword(?string $value): self
    {
        $this->keyword = $value ?? '';
        return $this;
    }
    
    public function getKeyword(): string
    {
        return $this->keyword;
    }
    
    public function setPageNo(int $value): self
    {
        $this->pageNo = $value;
        return $this;
    }
    
    public function getPageNo(): int
    {
        return $this->pageNo;
    }
    
    public function setMaxPerPage(int $value): self
    {
        $this->maxPerPage = $value;
        return $this;
    }
    
    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }
    
    // ... implement all other interface methods with proper type hints
}
```

### Example 5: Migrating from Deprecated Pricing Methods

Prices are now retrieved from Magento's price index. Remove custom price calculation logic and rely on the framework.

*Before (0.7.x):*
```php
<?php
namespace Vendor\Module\Model\Product\ProductType;

use HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType;
use Magento\Catalog\Model\Product;

class CustomProductType extends DefaultType
{
    /**
     * Get price data for indexing
     */
    public function getPriceData(Product $product, array $priceData = []): array
    {
        // Calculate min/max prices manually
        $minMaxPrices = $this->getMinMaxPrice($product);
        $priceData['price_min'] = $minMaxPrices['min'];
        $priceData['price_max'] = $minMaxPrices['max'];
        
        // Add prices including tax (deprecated)
        $priceData = $this->addPricesIncludingTax($product, $priceData);
        
        // Format prices (deprecated)
        $priceData = $this->addFormattedPrices($priceData);
        
        // Get tier prices manually (deprecated)
        $priceData['tier_prices'] = $this->getTierPrices($product);
        
        // Custom price handling logic
        if ($product->getSpecialPrice()) {
            $priceData['custom_discount'] = $this->calculateDiscount($product);
        }
        
        return $priceData;
    }
    
    /**
     * Custom discount calculation
     */
    private function calculateDiscount(Product $product): float
    {
        $regularPrice = (float)$product->getPrice();
        $specialPrice = (float)$product->getSpecialPrice();
        
        return round((($regularPrice - $specialPrice) / $regularPrice) * 100, 2);
    }
}
```

*After (0.8.0):*
```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Model\Product\ProductType;

use HawkSearch\EsIndexing\Model\Product\ProductType\DefaultType;
use Magento\Catalog\Model\Product;

class CustomProductType extends DefaultType
{
    /**
     * Get price data for indexing
     * 
     * Prices are automatically retrieved from Magento's catalog_product_index_price
     * No need to calculate manually
     */
    public function getPriceData(Product $product, array $priceData = []): array
    {
        // Get base price data from parent (includes prices from price index)
        $priceData = parent::getPriceData($product, $priceData);
        
        // Add custom fields if needed (non-price calculations)
        // Tax and formatting are handled by the frontend Vue component
        
        // Example: Add custom business logic that doesn't involve prices
        if ($product->getData('is_featured')) {
            $priceData['custom_featured'] = true;
        }
        
        return $priceData;
    }
}
```

**JavaScript Migration:**

*Before (0.7.x):*
```javascript
// custom-search.js
define([
    'hawksearch'
], function(hawksearch) {
    'use strict';
    
    return function(config, element) {
        // Legacy price rendering
        var formatPrice = function(item) {
            return hawksearch.formatItemPrice(item.Document || {});
        };
        
        // Display prices in custom component
        element.querySelector('.price').innerHTML = formatPrice(searchResult);
    };
});
```

*After (0.8.0):*
```javascript
// custom-search.js
define([
    'hawksearch'
], function(hawksearch) {
    'use strict';
    
    return function(config, element) {
        // Price rendering is handled by Vue component
        // No need for custom price formatting
        
        // If you need access to raw price data:
        var priceData = {
            regular: hawksearch.getDocumentField(item.Document, 'price_regular'),
            final: hawksearch.getDocumentField(item.Document, 'price_final'),
            filtered: hawksearch.getDocumentField(item.Document, 'price_filtered')
        };
        
        // Use Vue component for display
        // <product-price></product-price> handles all rendering
    };
});
```

**Template Migration:**

*Before (0.7.x):*
```html
<!-- custom-result-item.phtml -->
<div class="product-item">
    <span class="product-name">{{ result.Document.name }}</span>
    <span v-html="hawksearch.formatItemPrice(result.Document || {})"></span>
</div>
```

*After (0.8.0):*
```html
<!-- custom-result-item.phtml -->
<div class="product-item">
    <span class="product-name">{{ result.Document.name }}</span>
    <product-price></product-price>
</div>
```

## Upgrade Checklist

- [ ] Verify PHP 8.1+ is installed
- [ ] Verify Magento 2.4.4+ is installed
- [ ] Run `composer update hawksearch/esindexing` to version 0.8.0
- [ ] Review and update custom classes extending HawkSearch classes
- [ ] Replace direct protected property access with constructor injection
- [ ] Replace deprecated method overrides with plugin-based approach
- [ ] Remove calls to deprecated public methods
- [ ] Remove any usage of deprecated JavaScript configurations (`patterns`, `priceTemplates`)
- [ ] Remove calls to `hawksearch.formatItemPrice()` in custom JavaScript
- [ ] Replace price rendering with Vue `<product-price>` component
- [ ] Add type hints to custom interface implementations
- [ ] Add `declare(strict_types=1);` to custom module files
- [ ] Test custom indexers and field handlers
- [ ] Run `bin/magento setup:upgrade`
- [ ] Run `bin/magento setup:di:compile`
- [ ] Run `bin/magento cache:clean`
- [ ] Run `bin/magento indexer:reindex hawksearch_entities`
- [ ] Review deprecation warnings in logs (check `var/log/system.log`)
- [ ] Test field-attribute mapping in admin panel
- [ ] Test price display for all product types:
  - [ ] Simple products
  - [ ] Configurable products
  - [ ] Bundle products
  - [ ] Grouped products
  - [ ] Downloadable products
  - [ ] Virtual products
- [ ] Verify pricing fields is indexed correctly
- [ ] Test price range facets in search results
- [ ] Test sorting by price
- [ ] Verify special prices and discounts display correctly
- [ ] Verify index synchronization with Hawksearch API
- [ ] Test custom field handlers if any
- [ ] Verify autocomplete and search functionality on frontend
- [ ] Test price display in autocomplete suggestions
