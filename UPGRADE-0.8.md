# UPGRADE FROM 0.7 to 0.8

## Steps to action

- Upgrade your Magento store at least to version 2.4.4 and PHP to version 8.1
- Usage of protected property `Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache` is deprecated. Visibility will be changed to private in version 1.0.
- Usage of protected property `Model\Indexer\Entities\ActionAbstract::eventManager` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexer\Entities\ActionAbstract::messageManager` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexer\Entities\ActionAbstract::publisher` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexer\Entities\ActionAbstract::entityScheduler` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexer\Entities\SchedulerComposite::schedulers` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexing\AbstractEntityRebuild::entityTypePool` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexing\AbstractEntityRebuild::eventManager` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexing\AbstractEntityRebuild::hawkLogger` is deprecated. Visibility will be changed to private in version 1.0. Set via `$loggerFactory` constructor injection.
- Usage of protected property `Model\Indexing\AbstractEntityRebuild::storeManager` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexing\AbstractEntityRebuild::indexingContext` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
- Usage of protected property `Model\Indexing\FieldHandler\Composite::handlers` is deprecated. Visibility will be changed to private in version 1.0. Set via constructor injection.
