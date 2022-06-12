# [Debug](debug.md) / [Entities](entities.md) / Argument
 > im\debug\entities\Argument
____

## Description
Defines a function argument

## Synopsis
```php
class Argument implements Stringable {

    // Constants
    public T_BYREF = 32
    public T_VARIADIC = 64
    public T_OPTIONAL = 128

    // Methods
    public __construct(string $name, im\debug\entities\Type $type, int $flags = 0)
    public getName(): string
    public getType(): im\debug\entities\Type
    public isNullable(): bool
    public isOptional(): bool
    public isByRef(): bool
    public isVariadic(): bool
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__Argument&nbsp;::&nbsp;T\_BYREF__](entities-Argument-prop_T_BYREF.md) | This is a byref argument |
| [__Argument&nbsp;::&nbsp;T\_VARIADIC__](entities-Argument-prop_T_VARIADIC.md) | This is a variable length argument |
| [__Argument&nbsp;::&nbsp;T\_OPTIONAL__](entities-Argument-prop_T_OPTIONAL.md) | This is an optional (has a default value) argument |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Argument&nbsp;::&nbsp;\_\_construct__](entities-Argument-__construct.md) | Construct a new Argument |
| [__Argument&nbsp;::&nbsp;getName__](entities-Argument-getName.md) | Get the argument name |
| [__Argument&nbsp;::&nbsp;getType__](entities-Argument-getType.md) | Get the argument type |
| [__Argument&nbsp;::&nbsp;isNullable__](entities-Argument-isNullable.md) | Whether this is a nullable argument |
| [__Argument&nbsp;::&nbsp;isOptional__](entities-Argument-isOptional.md) | Whether this is an optional argument |
| [__Argument&nbsp;::&nbsp;isByRef__](entities-Argument-isByRef.md) | Whether this argument is passed by reference |
| [__Argument&nbsp;::&nbsp;isVariadic__](entities-Argument-isVariadic.md) | Whether this is a variable length argument |
