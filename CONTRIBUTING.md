## Backward compatibility policy

During active product development and feature improvement we make changes in minor or patch releases. From time to time,
some classes, interfaces and methods require to get updates in its structure and signature. We should perform any 
modification and to not break code that developers may be relying on. We follow the strategy of [Semantic Versioning](https://semver.org/) 
and develop all minor releases with a backward compatibility (BC) in mind.

In fact, almost every change can break an application. For example, if we add a new public or protected method to a class,
this may break an application in case, when someone has already added the same method but with a different signature 
in derived class. 

The goal of this document is to give a guidance for core and third party developers how to safely make changes 
in the existing codebase with the minimal efforts. Follow the rules and best practices for backward compatible 
development together with HawkSearch core team.

### Deprecation

HawkSearch Core development policy states that public API will not be changed unexpectedly. Whenever a new API is added, 
the old API is deprecated. Follow [Removal of deprecated code](#removal-of-deprecated-code) section for learning the terms 
when deprecation is still available.

#### Deprecation steps

Deprecating procedure consists of these steps:

1. Add `@deprecated` PHPdoc and follow it up with an explanation.
2. Use the `@see` tag to suggest a replacement. If `@see` tag can't be used suggest a replacement in deprecation message.
3. Keep previous code working.
4. Trigger a deprecation message `@trigger_error('...', E_USER_DEPRECATED)` in deprecated methods/classes 
   to notify developers about a deprecated functionality. The `@` suppression should be used in most cases 
   so that we can customize the error handling and avoid flooding logs on production.
5. Remove the code in the next major version or in accordance with the [deprecation removal schedule](#removal-of-deprecated-code).

#### Document deprecations

All deprecations should be recorded within `CHANGELOG.md` file under _DEPRECATIONS_ section for each minor release.
Entries to the _DEPRECATIONS_ section are made at the same time as the code is changed. During review phase the patch 
changes with deprecations should not pass a review if deprecations are not documented. Bellow is an example of
_DEPRECATIONS_ section in `CHANGELOG.md` file:

```markdown
## [0.7.0] - 2024-04-18

### DEPRECATIONS

* Class changes:
    - Model\ContentPage\Attribute\Handler\Url is deprecated,
      use Model\ContentPage\Field\Handler\Url

* Interface changes:
    - HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface is
      deprecated,
      use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface

* Di changes:
    - HawkSearch\EsIndexing\Model\Hierarchy\Attribute\Handler\Composite virtual type is deprecated
      use HawkSearch\EsIndexing\Model\Hierarchy\Field\Handler\Composite
```

### Deprecating classes/interfaces

- Add a `@deprecated` annotation to the class/interface doc comment and all methods, properties and constants
  doc comment so an IDE can highlight them as deprecated.
- If class/interface is deprecated in favour of a new API use `@see` annotation in class/interface linking to a new
  API which is used instead.
- Add a `@deprecated` phpdoc annotation and `@trigger_error('...', E_USER_DEPRECATED)` to the constructor. 
  If there is no constructor, add one and ensure it calls the parent constructor.
- Add `@trigger_error('...', E_USER_DEPRECATED)` before the interface/class declaration so that the deprecation error
  will be triggered in case of static class/interface usage.
- Extend an old class/interface from the new one if it logically makes sense. In this case the new API which has a 
  dependency on a new class/interface can still accept old interface instances. 

Deprecating Interface example:
```php
<?php

namespace HawkSearch\EsIndexing\Model\Indexing;

\HawkSearch\Connector\Compatibility\PublicContractDeprecation::triggerDeprecationMessage(
    AttributeHandlerInterface::class,
    '0.7.0',
    FieldHandlerInterface::class,
    'In favour of a new Field Handlers logic.'
);

/**
 * @deprecated 0.7.0 in favour of a new Field Handlers logic
 * @see FieldHandlerInterface 
 */
interface AttributeHandlerInterface extends FieldHandlerInterface
{
    // Old public interface methods have been moved to FieldHandlerInterface
}
```

Deprecating Class example:
```php
<?php

namespace HawkSearch\EsIndexing;

/**
 * @deprecated 0.7.0 because of removed logic
 */
class OldConcreteClass extends BaseClass
{
    /**
     * @var string
     * @deprecated 0.7.0 because of removed logic
     */
    protected $property;

    /**
     * @deprecated 0.7.0 because of removed logic
     */
    public function __construct()
    {
        @trigger_error(__CLASS__ .' is deprecated in 0.7.0. Please adopt your code to a new changes', E_USER_DEPRECATED);
        parent::__construct();
    }
    
    /**
     * @deprecated 0.7.0  because of removed logic
     * @return void
     */
    public function addPublicAction() 
    {
        // ... method logic
    }
    
    /**
     * @deprecated 0.7.0  because of removed logic
     * @return void
     */
    protected function addSomeProtectedData() 
    {
        // ... method logic
    }
}
```

### Deprecating methods

- Add `@deprecated` to the docblock for public and protected methods.
- Add `@trigger_error('...', E_USER_DEPRECATED)` at the top of the method.
- Continue returning the same results from the method if possible, so the old functionality is preserved.

```php
<?php

/**
 * @deprecated 0.7.0 because of unused logic
 * @return void
 */
public function addPublicAction() 
{
    @trigger_error(__METHOD__ .' is deprecated in 0.7.0. Please adopt your code to a new changes', E_USER_DEPRECATED);
    // ... method logic
}
```

### Adding new methods to interfaces and abstract classes

Adding a new method to an interface or an abstract method to a base abstract class is a breaking change.
Depending on the scenario the interface/abstract class is used in concrete application there are three options available 
to overcome the breaking change. 

#### Introduce an existing interface extension

This option doesn't refer to deprecation and will not lead to a breaking change in the future because it is a kind of 
optional logic concrete class can get. This technique can be achieved in the following steps:
- Create a new interface with a new method instead of introducing a method to an existing interface.
- All concrete classes which want to get a new logic introduced in the new method have to implement an extended 
  interface along with the old one.

Example:
```php
<?php

/**
 * This is the interface we want to extend with a new method
 */
interface OldInterface
{
    public function call();
}

/**
 * This is the extension of OldInterface via callExtended() method
 */
interface ExtendedOldInterface
{
    /**
     * This is a new method
     */
    public function callExtended();
}

class ConcreteClass implements OldInterface, ExtendedOldInterface
{
    public function call()
    {
        // implementation of call() method
    }
    
    public function callExtended()
    {
        // implementation of a new callExtended() method
    }
}
```

#### Fully deprecate an existing interface

Sometimes it is reasonable to deprecate the whole interface and create a new one with a new method. Reference 
[Deprecating classes/interfaces](#deprecating-classesinterfaces) for this option.

#### Add a new method to an existing interface

This is a common scenario for cases when interface referencing a DB entity or similar. We don't want to create the 
interface extension or fully deprecate the current interface. The steps for this option are the following:

- Add `@method` annotation to the interface doc comment
- Implement a new method in all classes implementing the interface
- Safely call a new method in all places where interface is used: check if new method exists in the class object using 
  `method_exists()` function and call it, call an old method otherwise.

### Deprecating properties

- Add `@deprecated` annotation tag to the property docblock.
- Continue storing the value in the property, so the old functionality is preserved.


### Adding a constructor argument

### Removing a constructor argument

### Adding a new method argument

#### public methods
#### protected methods

### Removing a method argument

### Modifying the default values of optional arguments in public and protected methods

### Changing scope: public, protected, private

### Converting a method scope from public to protected

### Converting a public property to protected


### Removing, renaming, or changing the type of event arguments

### Deprecations in Javascript


### Removal of deprecated code

**Before the first major release 1.0.0**, all deprecated code will not be removed the next 2 minor releases or until a major release. 
For example, if deprecation is announced in v0.8.0 it will be preserved until v0.10.0 and will be removed in v0.11.0 or in v1.0.0, 
depending on what happens first.  
**After the release 1.0.0**, all deprecated code will be removed on next major release.
