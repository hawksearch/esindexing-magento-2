# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2023-07-07

## FEATURES
- update parent products index when child is changed ([48019c6](https://github.com/hawksearch/esindexing-magento-2/commit/48019c6b78330033dbd59c7cc971a6bb6c518c81))
  When child product is updated/removed then all parents are updated in the index.
  When child product is assigned to parent product then parent one is updated in the index.
  Refs: [#HC-1403](https://bridgeline.atlassian.net/browse/HC-1403)

## FIXES
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

[Unreleased]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.4.0...HEAD
[0.3.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/hawksearch/esindexing-magento-2/compare/v0.2.0...v0.3.0
