# [Debug](debug.md) / [Entities](entities.md) / Clazz
 > im\debug\entities\Clazz
____

## Description
Defines a Class _(class, interface, trait or interface)_

## Synopsis
```php
class Clazz extends im\debug\entities\Entity implements Stringable, IteratorAggregate, Traversable {

    // Constants
    public T_CLASS = 1
    public T_INTERFACE = 5
    public T_TRAIT = 3
    public T_ENUM = 9
    public T_FINAL = 256
    public T_ABSTRACT = 512
    public T_ANONYMOUS = 4096

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(im\debug\entities\Name $name, int $flags = im\debug\entities\Clazz::T_CLASS, null|im\debug\entities\Name $extends = NULL, array $implements = Array)
    public isAnonymous(): bool
    public isFinal(): bool
    public isAbstract(): bool
    public isTrait(): bool
    public isInterface(): bool
    public isEnum(): bool
    public getName(): im\debug\entities\Name
    public getExtends(): null|im\debug\entities\Name
    public getImplement(string $label): null|im\debug\entities\Name
    public getImplements(): array
    public addTrait(im\debug\entities\Name $name): void
    public getTrait(string $label): null|im\debug\entities\Name
    public getTraits(): array
    public addProperty(im\debug\entities\Property $prop): void
    public getProperty(string $name): null|im\debug\entities\Property
    public getProperties(): array
    public addMethod(im\debug\entities\Routine $func): void
    public getMethod(string $name): null|im\debug\entities\Routine
    public getMethods(): array
    public getIterator(): Traversable
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__Clazz&nbsp;::&nbsp;T\_CLASS__](entities-Clazz-prop_T_CLASS.md) | This is a Class |
| [__Clazz&nbsp;::&nbsp;T\_INTERFACE__](entities-Clazz-prop_T_INTERFACE.md) | This is an Interface |
| [__Clazz&nbsp;::&nbsp;T\_TRAIT__](entities-Clazz-prop_T_TRAIT.md) | This is a Trait class |
| [__Clazz&nbsp;::&nbsp;T\_ENUM__](entities-Clazz-prop_T_ENUM.md) | This is an Enum class |
| [__Clazz&nbsp;::&nbsp;T\_FINAL__](entities-Clazz-prop_T_FINAL.md) | This class is Final |
| [__Clazz&nbsp;::&nbsp;T\_ABSTRACT__](entities-Clazz-prop_T_ABSTRACT.md) | This class is Abstract |
| [__Clazz&nbsp;::&nbsp;T\_ANONYMOUS__](entities-Clazz-prop_T_ANONYMOUS.md) | This class is Anonymous |

## Properties
| Name | Description |
| :--- | :---------- |
| [__Clazz&nbsp;::&nbsp;$line__](entities-Clazz-var_line.md) | The line number of where this entity begins |
| [__Clazz&nbsp;::&nbsp;$pos__](entities-Clazz-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Clazz&nbsp;::&nbsp;\_\_construct__](entities-Clazz-__construct.md) | Construct a new Class |
| [__Clazz&nbsp;::&nbsp;isAnonymous__](entities-Clazz-isAnonymous.md) | Whether this class is anonymous |
| [__Clazz&nbsp;::&nbsp;isFinal__](entities-Clazz-isFinal.md) | Whether this class is final |
| [__Clazz&nbsp;::&nbsp;isAbstract__](entities-Clazz-isAbstract.md) | Whether this class is abstract |
| [__Clazz&nbsp;::&nbsp;isTrait__](entities-Clazz-isTrait.md) | Whether this class is a trait class |
| [__Clazz&nbsp;::&nbsp;isInterface__](entities-Clazz-isInterface.md) | Whether this class is an interface |
| [__Clazz&nbsp;::&nbsp;isEnum__](entities-Clazz-isEnum.md) | Whether this class is an enum class |
| [__Clazz&nbsp;::&nbsp;getName__](entities-Clazz-getName.md) | Get the class name |
| [__Clazz&nbsp;::&nbsp;getExtends__](entities-Clazz-getExtends.md) | Get the parent class name |
| [__Clazz&nbsp;::&nbsp;getImplement__](entities-Clazz-getImplement.md) | Get a specified interface name |
| [__Clazz&nbsp;::&nbsp;getImplements__](entities-Clazz-getImplements.md) | Return all interface names used by this class |
| [__Clazz&nbsp;::&nbsp;addTrait__](entities-Clazz-addTrait.md) | Add a trait name to this class |
| [__Clazz&nbsp;::&nbsp;getTrait__](entities-Clazz-getTrait.md) | Get a specified trait name |
| [__Clazz&nbsp;::&nbsp;getTraits__](entities-Clazz-getTraits.md) | Return all trait names used by this class |
| [__Clazz&nbsp;::&nbsp;addProperty__](entities-Clazz-addProperty.md) | Add a property to this class |
| [__Clazz&nbsp;::&nbsp;getProperty__](entities-Clazz-getProperty.md) | Get a specified property |
| [__Clazz&nbsp;::&nbsp;getProperties__](entities-Clazz-getProperties.md) | Return all properties within by this class |
| [__Clazz&nbsp;::&nbsp;addMethod__](entities-Clazz-addMethod.md) | Add a method to this class |
| [__Clazz&nbsp;::&nbsp;getMethod__](entities-Clazz-getMethod.md) | Get a specified method |
| [__Clazz&nbsp;::&nbsp;getMethods__](entities-Clazz-getMethods.md) | Return all methods within by this class |
| [__Clazz&nbsp;::&nbsp;getIterator__](entities-Clazz-getIterator.md) | Provides a Traversable to iterate through all members |
