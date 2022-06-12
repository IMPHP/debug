# [Debug](debug.md) / [Entities](entities.md) / Member
 > im\debug\entities\Member
____

## Description
Defines a class member

This is used for things like a method,
property, enum case etc.

## Synopsis
```php
abstract class Member extends im\debug\entities\Entity implements Stringable {

    // Constants
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
| [__Member&nbsp;::&nbsp;T\_PUBLIC__](entities-Member-prop_T_PUBLIC.md) | This is a public member |
| [__Member&nbsp;::&nbsp;T\_PROTECTED__](entities-Member-prop_T_PROTECTED.md) | This is a protected member |
| [__Member&nbsp;::&nbsp;T\_PRIVATE__](entities-Member-prop_T_PRIVATE.md) | This is a private member |
| [__Member&nbsp;::&nbsp;T\_STATIC__](entities-Member-prop_T_STATIC.md) | This is a static member |
| [__Member&nbsp;::&nbsp;T\_FINAL__](entities-Member-prop_T_FINAL.md) | This is a final member |
| [__Member&nbsp;::&nbsp;T\_META__](entities-Member-prop_T_META.md) | Meta that defines all member modifier flags |

## Properties
| Name | Description |
| :--- | :---------- |
| [__Member&nbsp;::&nbsp;$line__](entities-Member-var_line.md) | The line number of where this entity begins |
| [__Member&nbsp;::&nbsp;$pos__](entities-Member-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Member&nbsp;::&nbsp;\_\_construct__](entities-Member-__construct.md) | Construct a new Member |
| [__Member&nbsp;::&nbsp;getType__](entities-Member-getType.md) | Get the type of this member |
| [__Member&nbsp;::&nbsp;getName__](entities-Member-getName.md) | Get the name of this member |
| [__Member&nbsp;::&nbsp;isPublic__](entities-Member-isPublic.md) | Whether this member is public |
| [__Member&nbsp;::&nbsp;isProtected__](entities-Member-isProtected.md) | Whether this member is protected |
| [__Member&nbsp;::&nbsp;isPrivate__](entities-Member-isPrivate.md) | Whether this member is private |
| [__Member&nbsp;::&nbsp;isStatic__](entities-Member-isStatic.md) | Whether this member is static |
| [__Member&nbsp;::&nbsp;isFinal__](entities-Member-isFinal.md) | Whether this member is final |
