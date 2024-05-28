# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

See tasks currently in development on [Unreleased] changes page.

## 0.7.0 - [Unreleased]

### DEPRECATIONS

* Class changes:
  - HawkSearch\EsIndexing\Model\ContentPage\Attribute\Handler\Url is deprecated,
    use HawkSearch\EsIndexing\Model\ContentPage\Field\Handler\Url
  - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\HierarchyId is deprecated,
    use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\HierarchyId
  - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\IsActive is deprecated,
    use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\IsActive
  - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\Name is deprecated,
    use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\Name
  - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\ParentHierarchyId is deprecated,
    use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\ParentHierarchyId
  - HawkSearch\EsIndexing\Model\Indexing\AttributeHandler\Composite is deprecated,
    use HawkSearch\EsIndexing\Model\Indexing\FieldHandler\Composite
  - HawkSearch\EsIndexing\Model\Indexing\AttributeHandler\DataObjectHandler is deprecated,
    use HawkSearch\EsIndexing\Model\Indexing\FieldHandler\DataObjectHandler
  - HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler\CustomSortList is deprecated,
    use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\CustomSortList
  - HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler\CustomUrl is deprecated,
    use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\CustomUrl
  - HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler\DefaultHandler is deprecated,
    use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\DefaultHandler
  - HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler\NarrowXml is deprecated,
    use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\NarrowXml
  - HawkSearch\EsIndexing\Model\Product\Attribute\Handler\Category is deprecated,
    use HawkSearch\EsIndexing\Model\Product\Field\Handler\Category
  - HawkSearch\EsIndexing\Model\Product\Attribute\Handler\Composite is deprecated,
    use HawkSearch\EsIndexing\Model\Product\Field\Handler\Composite
  - HawkSearch\EsIndexing\Model\Product\Attribute\Handler\DefaultHandler is deprecated,
    use HawkSearch\EsIndexing\Model\Product\Field\Handler\DefaultHandler
  - HawkSearch\EsIndexing\Model\Product\Attribute\Handler\ImageUrl is deprecated,
    use HawkSearch\EsIndexing\Model\Product\Field\Handler\ImageUrl
  - HawkSearch\EsIndexing\Model\Product\Attribute\Handler\Url is deprecated,
    use HawkSearch\EsIndexing\Model\Product\Field\Handler\Url
  - Parameter $attributeHandler is deprecated in method
    HawkSearch\EsIndexing\Model\Indexing\EntityType::__construct(),
    use $fieldHandler
  - HawkSearch\EsIndexing\Model\Indexing\EntityType\EntityTypeAbstract::getAttributeHandler()
    is deprecated,
    use HawkSearch\EsIndexing\Model\Indexing\EntityType\EntityTypeAbstract::getFieldHandler()

* Interface changes:
  - HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface is
    deprecated,
    use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface
  - HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface::getAttributeHandler()
    is deprecated,
    use HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface::getFieldHandler()

* Di changes:
  - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\Composite virtual type is deprecated
    use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\Composite
  - HawkSearch\EsIndexing\Model\ContentPage\Attribute\Handler\Composite virtual type is deprecated
    use HawkSearch\EsIndexing\Model\ContentPage\Field\Handler\Composite
  - HawkSearch\EsIndexing\Model\LandingPage\Attribute\Handler\Composite virtual type is deprecated
    use HawkSearch\EsIndexing\Model\LandingPage\Field\Handler\Composite

## [0.6.4] - 2024-05-08

## FIXES

* __fix: shared entity type cache not updating attributes__ ([3787516](https://github.com/hawksearch/esindexing-magento-2/commit/378751629cb40300dd650c55b159720b332bbbf7))
  
  Shared EntityType per long running consumer brought an issue with
  cached private properties.
  Make AttributeHandler for ProductEntityType not shared.

  Ref: HC-1630

* __fix: don't rollup system attribute values to parent product__ ([96b30e4](https://github.com/hawksearch/esindexing-magento-2/commit/96b30e41189990798889b817a26510d9b8b30091))
  
  Values of system attributes shouldn’t be rolled up to the parent product.
  System attributes are: category, status, visibility, url , image_url,
  thumbnail_url, type_id

  Ref: HC-1503

* __fix: child attribute values doesn't roll-up to parent product__ ([62170fc](https://github.com/hawksearch/esindexing-magento-2/commit/62170fc2b1293182bf0a4fc6dd4575b33c2a1da5))
  
  Ref: HC-1630


## [0.6.3] - 2024-04-25

### FIXES

* __fix: minicart not updated after adding product to cart__ ([#57](https://github.com/hawksearch/esindexing-magento-2/pull/57))

  ref: HC-1612

## [0.6.2] - 2024-03-20

### FIXES

* __fix: sql error on indexing product with no categories__ ([9989ea6](https://github.com/hawksearch/esindexing-magento-2/commit/9989ea64562a190ee80ea03395ad7b52f0ce6bea))
  
  ref: HC-1606


## [0.6.1] - 2024-03-05

### FIXES

* __fix: popular searches and content matches not clickable__ ([#51](https://github.com/hawksearch/esindexing-magento-2/pull/51))
  
  Update hawksearch-vue lib to version 0.9.103

  ref: HC-1264, AIC-30, HC-1587

* __fix: wrong interface for non-product attributes on field mapping saving__ ([e958760](https://github.com/hawksearch/esindexing-magento-2/commit/e9587604bdd049b7df43159ccbd2cf904e7285ed))
  
  ref: HC-1595

* __fix: empty field mapping configuration breaks on saving__ ([#50](https://github.com/hawksearch/esindexing-magento-2/pull/50))

  ref: HC-1590

* __fix: consumer processes only open bulk operations__ ([#49](https://github.com/hawksearch/esindexing-magento-2/pull/49))

  If there were "not started" messages in message queue but these
  messages were linked to completed, failed or rejected bulk operations
  then consumer processed such operations again. This behavior is fixed.

  ref: HC-1552

## [0.6.0] - 2024-02-09

### FEATURES

* __feat: add price to suggestion item (autocomplete)__ ([#47](https://github.com/hawksearch/esindexing-magento-2/pull/47))
  
  Ref: HC-1561

* __feat: update hawksearch-vue lib to version 0.9.102__ ([220d34e](https://github.com/hawksearch/vue-hawksearch/commit/220d34e39a413cf86961ee07deddaa486b6ab2c7)) ([a3385bb](https://github.com/hawksearch/vue-hawksearch/commit/a3385bb8f466a0d070ace9d98220e28886ff1c89)) ([0.9.80...v0.9.102](https://github.com/hawksearch/vue-hawksearch/compare/0.9.80...v0.9.102))

    - implementation of Size Facet type
    - fix html entities were not decoded
    - color swatch facet values were not filtered to result
    - hierarchical facet check boxes could not be updated
    - fix striking through the text of option which negated
    - fix unchecking of checkbox for Nested Facets

  Ref: HC-1573, HC-1559, HC-1560, HC-1203, HC-1557

* __feat: add Size facet type__ ([#47](https://github.com/hawksearch/esindexing-magento-2/pull/47))

  - move Vue widget initialization out of bundled vue-hawksearch-app.js file to common.js file

  Ref: HC-1204

### FIXES

* __fix: zero price for complex products__ ([#47](https://github.com/hawksearch/esindexing-magento-2/pull/47))
  
  Configurable, Bundle and Grouped products displayed price as zero
  in catalog

  Ref: HC-1574
* __fix: display price label based on product type__ ([#47](https://github.com/hawksearch/esindexing-magento-2/pull/47))
  
  Price label on search results is displayed based on product type:
  - Simple products - $###
  - Configurable products - As low as: $###

  Ref: HC-1562

## [0.5.1] - 2024-01-18

### FIXES

__fix: minimal compatible version of connector package is 2.8.0__ ([#45](https://github.com/hawksearch/esindexing-magento-2/pull/45))

## [0.5.0] - 2024-01-18

### FEATURES

* __feat: add tracking for add2cart and sale events__ ([#34](https://github.com/hawksearch/esindexing-magento-2/pull/34))

  - Implement event-tracking javascript library.
  - Add an ability to exclude events which won't be fired.
  - Add DataStorage service.
  - Support [sale](https://developerdocs.hawksearch.com/docs/event-tracking-api#sale-event) tracking event on Order complete (Order Success Page).
  - Support [add2cart](https://developerdocs.hawksearch.com/docs/event-tracking-api#add-to-cart-event) tracking event.

  Refs: HC-1392
* __feat: update hawksearch-vue lib to version 0.9.101__ ([#34](https://github.com/hawksearch/esindexing-magento-2/pull/34))

  - let firing tracking events on search item link click ([821b418](https://github.com/hawksearch/vue-hawksearch/commit/821b418396f334134509d3285029b15e3b49b686))
  - allow item to be opened in new tab by short key ([821b418](https://github.com/hawksearch/vue-hawksearch/commit/821b418396f334134509d3285029b15e3b49b686))
  - change 'getLink()' method by computed property 'link' in :href attribute ([bc51ed7](https://github.com/hawksearch/vue-hawksearch/commit/bc51ed762d895a68be804bbab9822210b2fce0f5))

    Refs: HC-1392
* __feat: disable indexing by default__

  - '_Enable Indexing_' config now only available on Store View scope.
  - '_Enable Indexing_ config default value  is changed to "No".
  - Indexing is disable by default for a new created store. It prevents indexing to be started on new Stores

  Refs: HC-1449
* __feat: sync attribute properties to field and facet on config save__

  Field properties in Hawksearch are synchronized with Magento's attribute properties 'Is Filterable', 'Is Searchable' 
  and ‘Used for Sorting in Product Listing’ when HawkSearch Products > Products > Attributes setting is saved.
  'Is Filterable' property of an attribute also synced to a facet associated with the field.

  Add Field add Facet interfaces for API. Do not use \HawkSearch\Connector\Api\Data\HawkSearchFieldInterface anymore.

  Refs: HC-1407
* __feat: add crontab to retry failed indexing operations__ ([#41](https://github.com/hawksearch/esindexing-magento-2/pull/41))

  Refs HC-1447

### FIXES

* __fix: cannot read properties of undefined in getID function__ ([#26](https://github.com/hawksearch/esindexing-magento-2/pull/26))

  Refs: HC-1460
* __fix: make attribute values of complex products unique__ ([#36](https://github.com/hawksearch/esindexing-magento-2/pull/36))

  - Do not roll-up values of SKU attribute of children to parent
  - Roll-up attribute values of children to parent and make them unique

  Refs: HC-1394, HC-1501, HC-1502, RR-14
* __fix: bad request on add2cart event tracking request__ ([#38](https://github.com/hawksearch/esindexing-magento-2/pull/38))

  Refs: HC-1453
* __fix: use price including tax on add2cart event tracking request__ ([#39](https://github.com/hawksearch/esindexing-magento-2/pull/39))
* __fix: add missing cronjob argument__ ([#42](https://github.com/hawksearch/esindexing-magento-2/pull/42))
* __fix: wrong page redirect after adding product to cart__ ([6d88bbb](https://github.com/hawksearch/esindexing-magento-2/commit/6d88bbbc3bef0e1b4fab5ddd2bfde1430261406d))

  When product was added to cart from the page rendered by Vue application with changed URL parameters, 
  then browser redirected to the initial page rendered by force page reloading. Now `uenc` parameter in `tocart-form` 
  is updated according to changed URL parameters.

  Refs: HC-1534, AIC-20
* __fix: invalid form key on add to cart form__([#44](https://github.com/hawksearch/esindexing-magento-2/pull/44))

  Refs: HC-1542, AIC-19

## [0.4.2] - 2023-10-12

### FIXES
* __fix: remove hierarchyRebuild message form the queue__ ([c480c34](https://github.com/hawksearch/esindexing-magento-2/commit/c480c343825fc6f8a68b241366cf34df594ca024))

  Ref ([#HC-1494](https://bridgeline.atlassian.net/browse/HC-1494))

* __fix: syntax error on PHP less than 8.0__ ([dfd0a71](https://github.com/hawksearch/esindexing-magento-2/commit/dfd0a71135c592787592ba3685480df4cf5f521f))
  
  Fixes ([#HC-1495](https://bridgeline.atlassian.net/browse/HC-1495))

## [0.4.1] - 2023-09-27

### FIXES
* __fix: remove dependency on CatalogStaging module__ ([f755f93](https://github.com/hawksearch/esindexing-magento-2/commit/f755f9310c551984fa7330b9a1a655bc4ac16f0c))
  
  Unable to install the extension because of compatibility issue:
  magento/module-catalog-staging is exclusive to Adobe Commerce only.
  Make v0.4.0 features compatible with Magento Open Source.

  Ref #HC-1480

* __fix: open links in new tab__ ([8d2f64f](https://github.com/hawksearch/esindexing-magento-2/commit/8d2f64f3762c4fca59a1870f1f663fd191804b22))
  
  There were not possible to open links
  Fix issues in @hawksearch/vue library and update it to v0.9.98

  Fixes HC-1474, HS-2601

* __fix: unknown column started_at__ ([49a358e](https://github.com/hawksearch/esindexing-magento-2/commit/49a358ea999c26b050dbe9ed2c973bd268e14d9a))
  
  On Magento Open Source and Adobe Commerce version 2.4.3 and less
  was no column ‘started_at’ in the  magento_operation table.
  As a result, an error occurred on the page Indexing Bulks

  Fixes HC-1472

* __fix: make Indexing Bulks menu visible on Magento Open Source__ ([803a986](https://github.com/hawksearch/esindexing-magento-2/commit/803a986227f3f4f02a8f2ffea816854fed5af7e4))

* __fix: js error cannot read properties of undefined in getID function__ ([07f5351](https://github.com/hawksearch/esindexing-magento-2/commit/07f53511d998841896f57a0e05c8497652634877))

## [0.4.0] - 2023-07-07

### FEATURES
- update parent products index when child is changed ([48019c6](https://github.com/hawksearch/esindexing-magento-2/commit/48019c6b78330033dbd59c7cc971a6bb6c518c81))
  When child product is updated/removed then all parents are updated in the index.
  When child product is assigned to parent product then parent one is updated in the index.
  Refs: [#HC-1403](https://bridgeline.atlassian.net/browse/HC-1403)

### FIXES
- duplicate async operations with the same ID in collection ([163daf5](https://github.com/hawksearch/esindexing-magento-2/commit/163daf5b25894bc9fd903b267cfc83c60198c322))
- invalid array index because of nonexistent category when indexing ([ed3253b](https://github.com/hawksearch/esindexing-magento-2/commit/ed3253b00786c27ed98687bb48cb15cc4bf037ac))
  Refs: [#HC-1437](https://bridgeline.atlassian.net/browse/HC-1437)
- class LoggerFactory implements LoggerFactoryInterface ([88e4817](https://github.com/hawksearch/esindexing-magento-2/commit/88e4817cd0238aa0ffd4039f12ad6b582b6b8a47))
  Deprecated LoggerFactory didn't implement deprecated LoggerFactoryInterface
  Refs [#HC-1441](https://bridgeline.atlassian.net/browse/HC-1441)
- categories with empty URL break LandingPage update API ([a8e5123](https://github.com/hawksearch/esindexing-magento-2/commit/a8e512342582d569152e728695cd67ad054d14e5))
  Refs [#HC-1442](https://bridgeline.atlassian.net/browse/HC-1442)
- reduce items_batch_size config value to 125 ([ca95985](https://github.com/hawksearch/esindexing-magento-2/commit/ca959858399e0193a3797ce847286879580c6134))
  Refs: [#HC-1437](https://bridgeline.atlassian.net/browse/HC-1437)
- update logger file location ([0d4c3cf](https://github.com/hawksearch/esindexing-magento-2/commit/0d4c3cf1db073db6054c5bf20263b57b3a58683e))
  Refs: [#HC-1437](https://bridgeline.atlassian.net/browse/HC-1437)

## [0.3.0] - 2023-04-06
### ADDED
- Rework UI interface for mapping Hawksearch fields to  Magento attributes ([#5](https://github.com/hawksearch/esindexing-magento-2/pull/5)),
  Refs: [#HC-1227](https://bridgeline.atlassian.net/browse/HC-1227)
- Add listing of Hawksearch scheduled bulks ([#7](https://github.com/hawksearch/esindexing-magento-2/pull/7)),
  Refs: [#HC-1317](https://bridgeline.atlassian.net/browse/HC-1317)
- Add "Hawksearch Categories" indexer.
  Add LandingPage entity in `hawksearch.esindexing` consumer ([#9](https://github.com/hawksearch/esindexing-magento-2/pull/9)),
  Refs: [#HC-1400](https://bridgeline.atlassian.net/browse/HC-1400)
- Add support for sorting by product position on category pages ([9dfcdbf](https://github.com/hawksearch/esindexing-magento-2/commit/9dfcdbf6e69854dc694b6b4e6d70656fc731a5de)),
  Refs: [#HC-1213](https://bridgeline.atlassian.net/browse/HC-1213), [#RR-28](https://bridgeline.atlassian.net/browse/RR-28)

### UPDATED
- Update Hawksearch indexers depenndencies ([e0510ad](https://github.com/hawksearch/esindexing-magento-2/commit/e0510ad9b205498a9c20b82c3fef5badb8fbd03c)),
  Refs: [#HC-1400](https://bridgeline.atlassian.net/browse/HC-1400)
- Invalidate indexers on Store view and Store group changes, after Product attribute changes ([#13](https://github.com/hawksearch/esindexing-magento-2/pull/13)),
  Refs: [#HC-1406](https://bridgeline.atlassian.net/browse/HC-1406)

### FIXED
- Fix ACL in System Configuration for Hawksearch sections ([#6](https://github.com/hawksearch/esindexing-magento-2/pull/6)),
  Refs: [#HC-1315](https://bridgeline.atlassian.net/browse/HC-1315)
- Fix Landing page is not updated after editing Magento category for the second time ([#10](https://github.com/hawksearch/esindexing-magento-2/pull/10))
- Fix consumer is crashing when sync landing pages ([9ab04ff](https://github.com/hawksearch/esindexing-magento-2/commit/9ab04fffe5c9a2ca3d3549ff76930e74c53328bb))
- Fix bug when product stock status changes are not taken into account when consumer is continuously running ([98364c3](https://github.com/hawksearch/esindexing-magento-2/commit/98364c3f8f9f97f36cff6897b2c67e9b57a7f797))
- Fix fatal error during reindexing when there are no inexes in the Hawksearch Engine ([#11](https://github.com/hawksearch/esindexing-magento-2/pull/11)),
  Refs: [#HC-58](https://bridgeline.atlassian.net/browse/HC-58)
- Update indexers after product import ([dcddbc0](https://github.com/hawksearch/esindexing-magento-2/commit/dcddbc0b4b02d868944e5d935654ded8523978c4)),
  Refs: [#HC-1322](https://bridgeline.atlassian.net/browse/HC-1322)
- Fix linked Category are not updated after product save ([#12](https://github.com/hawksearch/esindexing-magento-2/pull/12)),
  Refs: [#HC-1405](https://bridgeline.atlassian.net/browse/HC-1405)
- Fix linked Category are not updated after product delete ([#14](https://github.com/hawksearch/esindexing-magento-2/pull/14)),
  Refs: [#HC-1405](https://bridgeline.atlassian.net/browse/HC-1405)
- Deactivate landing pages cache to fix category status updates issue ([#15](https://github.com/hawksearch/esindexing-magento-2/pull/15)),
  Refs: [#HC-1401](https://bridgeline.atlassian.net/browse/HC-1401)

### REMOVED
- Remove hawksearch:sync-categories CLI command ([#9](https://github.com/hawksearch/esindexing-magento-2/pull/9)),
  Refs: [#HC-1400](https://bridgeline.atlassian.net/browse/HC-1400)



## 0.2.0
### FEATURES
- Add statuses argument to `hawksearch:retry-bulk` CLI command (#HC-1326)
- Allow to run full reindexing bulk asynchronously. Add support for multiple queue consumers (#HC-1323)
 
### UPDATES
- Update URL configuration references, default Hawksearch URLs after migration to AWS (#HC-1312)
- Add an ability to configure Search API and indexing API URLs in Admin panel
- Add full hierarchy parents tree reference when indexing product items. The configuration flag is added to switch
  it on and off. It makes nested category facet working (#CR-22)

### Fixes
- Fix traling slash issue in builcding API urls (#HC-1279)
- Fix disabled products are not removed from the current index (#HC-1309)
- Fix 'Passing null to non-nullable parameters of built-in functions' deprecation in PHP 8.1
- Fix wrong array format of complex products attributes (#HC-1254)
- Remove customUrl parameter from search params when searching from category page (#HC-1298)
- Fix consumers are failing if they are processed out of the order  

## 0.1.1
### UPDATES
- Add Magento 2.4.4 and PHP 8.1  compatibility
- Add support for DataObjectHelper updated in Magento 2.4.4

## 0.1.0
Initial stable release

[Unreleased]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.6.4...HEAD
[0.6.4]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.6.3...v0.6.4
[0.6.3]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.6.2...v0.6.3
[0.6.2]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.6.1...v0.6.2
[0.6.1]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.5.1...v0.6.0
[0.5.1]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.4.2...v0.5.0
[0.4.2]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.4.1...v0.4.2
[0.4.1]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.2.0...v0.3.0
