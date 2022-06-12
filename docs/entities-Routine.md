# [Debug](debug.md) / [Entities](entities.md) / Routine
 > im\debug\entities\Routine
____

## Description
Defines a Routine (function)

This can be anything from a closure to a class method
or a regular function.

## Synopsis
```php
class Routine extends im\debug\entities\Member implements Stringable, IteratorAggregate, Traversable {

    // Constants
    public T_BYREF = 32
    public T_ABSTRACT = 512
    public T_ANONYMOUS = 4096

    // Inherited Constants
    public T_PUBLIC = 8192
    public T_PROTECTED = 16384
    public T_PRIVATE = 32768
    public T_STATIC = 1024
    public T_FINAL = 256
    public T_META = 1048320

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(im\debug\entities\Name $name, im\debug\entities\Type $type, int $flags = im\debug\entities\Member::T_PUBLIC, array $params = Array)
    public isByRef(): bool
    public isAbstract(): bool
    public isAnonymous(): bool
    public getArgument(string $name): null|im\debug\entities\Argument
    public getIterator(): Traversable
    public addClass(im\debug\entities\Clazz $class): void
    public getClasses(): array
    public addFunction(im\debug\entities\Routine $func): void
    public getFunctions(): array

    // Inherited Methods
    public getType(): im\debug\entities\Type
    public getName(): im\debug\entities\Name
    public isPublic(): bool
    public isProtected(): bool
    public isPrivate(): bool
    public isStatic(): bool
    public isFinal(): bool
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__Routine&nbsp;::&nbsp;T\_BYREF__](entities-Routine-prop_T_BYREF.md) | This is a byref function return |
| [__Routine&nbsp;::&nbsp;T\_ABSTRACT__](entities-Routine-prop_T_ABSTRACT.md) | This is an abstract member |
| [__Routine&nbsp;::&nbsp;T\_ANONYMOUS__](entities-Routine-prop_T_ANONYMOUS.md) | This is anonymous member |
| [__Routine&nbsp;::&nbsp;T\_PUBLIC__](entities-Routine-prop_T_PUBLIC.md) | This is a public member |
| [__Routine&nbsp;::&nbsp;T\_PROTECTED__](entities-Routine-prop_T_PROTECTED.md) | This is a protected member |
| [__Routine&nbsp;::&nbsp;T\_PRIVATE__](entities-Routine-prop_T_PRIVATE.md) | This is a private member |
| [__Routine&nbsp;::&nbsp;T\_STATIC__](entities-Routine-prop_T_STATIC.md) | This is a static member |
| [__Routine&nbsp;::&nbsp;T\_FINAL__](entities-Routine-prop_T_FINAL.md) | This is a final member |
| [__Routine&nbsp;::&nbsp;T\_META__](entities-Routine-prop_T_META.md) | Meta that defines all member modifier flags |

## Properties
| Name | Description |
| :--- | :---------- |
| [__Routine&nbsp;::&nbsp;$line__](entities-Routine-var_line.md) | The line number of where this entity begins |
| [__Routine&nbsp;::&nbsp;$pos__](entities-Routine-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Routine&nbsp;::&nbsp;\_\_construct__](entities-Routine-__construct.md) | Construct a new Routine |
| [__Routine&nbsp;::&nbsp;isByRef__](entities-Routine-isByRef.md) | Whether this routine returns by reference |
| [__Routine&nbsp;::&nbsp;isAbstract__](entities-Routine-isAbstract.md) | Whether this member is abstract |
| [__Routine&nbsp;::&nbsp;isAnonymous__](entities-Routine-isAnonymous.md) | Whether this member is anonymous |
| [__Routine&nbsp;::&nbsp;getArgument__](entities-Routine-getArgument.md) | Get a specified argument |
| [__Routine&nbsp;::&nbsp;getIterator__](entities-Routine-getIterator.md) | Provides a Traversable to iterate through all arguments |
| [__Routine&nbsp;::&nbsp;addClass__](entities-Routine-addClass.md) | Add an anonymous class |
| [__Routine&nbsp;::&nbsp;getClasses__](entities-Routine-getClasses.md) | Return all of the anonymous classes within this routine |
| [__Routine&nbsp;::&nbsp;addFunction__](entities-Routine-addFunction.md) | Add a closure |
| [__Routine&nbsp;::&nbsp;getFunctions__](entities-Routine-getFunctions.md) | Return all of the closures within this routine |
| [__Routine&nbsp;::&nbsp;getType__](entities-Routine-getType.md) | Get the type of this member |
| [__Routine&nbsp;::&nbsp;getName__](entities-Routine-getName.md) | Get the name of this member |
| [__Routine&nbsp;::&nbsp;isPublic__](entities-Routine-isPublic.md) | Whether this member is public |
| [__Routine&nbsp;::&nbsp;isProtected__](entities-Routine-isProtected.md) | Whether this member is protected |
| [__Routine&nbsp;::&nbsp;isPrivate__](entities-Routine-isPrivate.md) | Whether this member is private |
| [__Routine&nbsp;::&nbsp;isStatic__](entities-Routine-isStatic.md) | Whether this member is static |
| [__Routine&nbsp;::&nbsp;isFinal__](entities-Routine-isFinal.md) | Whether this member is final |
