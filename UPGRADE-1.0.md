# UPGRADE FROM 0.x to 1.0

## Steps to action

- Protected property `Block\Adminhtml\System\Config\Product\CustomAttributes::columnRendererCache` visibility has been changed to `private`.
- Protected property `Model\Indexer\Entities\ActionAbstract::eventManager` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexer\Entities\ActionAbstract::messageManager` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexer\Entities\ActionAbstract::publisher` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexer\Entities\ActionAbstract::entityScheduler` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexer\Entities\SchedulerComposite::schedulers` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexing\AbstractEntityRebuild::entityTypePool` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexing\AbstractEntityRebuild::eventManager` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexing\AbstractEntityRebuild::hawkLogger` visibility has been changed to `private`. Set via `$loggerFactory` constructor injection.
- Protected property `Model\Indexing\AbstractEntityRebuild::storeManager` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexing\AbstractEntityRebuild::indexingContext` visibility has been changed to `private`. Set via constructor injection.
- Protected property `Model\Indexing\FieldHandler\Composite::handlers` visibility has been changed to `private`. Set via constructor injection.
