# [Debug](debug.md) / [Entities](entities.md) / Property
 > im\debug\entities\Property
____

## Description
Defines a property

This can be class variable or constant as well as
a global constant.

## Synopsis
```php
class Property extends im\debug\entities\Member implements Stringable {

    // Constants
    public T_READONLY = 2048
    public T_CASE = 65536

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
    public isReadonly(): bool
    public isEnumCase(): bool

    // Inherited Methods
    public __construct(im\debug\entities\Name $name, im\debug\entities\Type $type, int $flags = im\debug\entities\Member::T_PUBLIC)
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
| [__Property&nbsp;::&nbsp;T\_READONLY__](entities-Property-prop_T_READONLY.md) | This is a readonly member |
| [__Property&nbsp;::&nbsp;T\_CASE__](entities-Property-prop_T_CASE.md) | This is an enum case member |
| [__Property&nbsp;::&nbsp;T\_PUBLIC__](entities-Property-prop_T_PUBLIC.md) | This is a public member |
| [__Property&nbsp;::&nbsp;T\_PROTECTED__](entities-Property-prop_T_PROTECTED.md) | This is a protected member |
| [__Property&nbsp;::&nbsp;T\_PRIVATE__](entities-Property-prop_T_PRIVATE.md) | This is a private member |
| [__Property&nbsp;::&nbsp;T\_STATIC__](entities-Property-prop_T_STATIC.md) | This is a static member |
| [__Property&nbsp;::&nbsp;T\_FINAL__](entities-Property-prop_T_FINAL.md) | This is a final member |
| [__Property&nbsp;::&nbsp;T\_META__](entities-Property-prop_T_META.md) | Meta that defines all member modifier flags |

## Properties
| Name | Description |
| :--- | :---------- |
| [__Property&nbsp;::&nbsp;$line__](entities-Property-var_line.md) | The line number of where this entity begins |
| [__Property&nbsp;::&nbsp;$pos__](entities-Property-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Property&nbsp;::&nbsp;isReadonly__](entities-Property-isReadonly.md) | Whether this member is readonly |
| [__Property&nbsp;::&nbsp;isEnumCase__](entities-Property-isEnumCase.md) | Whether this member is an enum case |
| [__Property&nbsp;::&nbsp;\_\_construct__](entities-Property-__construct.md) | Construct a new Member |
| [__Property&nbsp;::&nbsp;getType__](entities-Property-getType.md) | Get the type of this member |
| [__Property&nbsp;::&nbsp;getName__](entities-Property-getName.md) | Get the name of this member |
| [__Property&nbsp;::&nbsp;isPublic__](entities-Property-isPublic.md) | Whether this member is public |
| [__Property&nbsp;::&nbsp;isProtected__](entities-Property-isProtected.md) | Whether this member is protected |
| [__Property&nbsp;::&nbsp;isPrivate__](entities-Property-isPrivate.md) | Whether this member is private |
| [__Property&nbsp;::&nbsp;isStatic__](entities-Property-isStatic.md) | Whether this member is static |
| [__Property&nbsp;::&nbsp;isFinal__](entities-Property-isFinal.md) | Whether this member is final |
