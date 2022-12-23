# CHANGELOG

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
