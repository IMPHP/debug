# [Debug](debug.md) / [Entities](entities.md) / Type
 > im\debug\entities\Type
____

## Description
Defines a type

This is an extension to `im\debug\entities\Name`
which also supports native (builtin) types.

## Synopsis
```php
class Type extends im\debug\entities\Name implements Stringable {

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(string $path, null|string $alias = NULL, bool $native = FALSE)
    public isNative(): bool

    // Inherited Methods
    public getLabel(): string
    public getName(): string
    public getNamespace(): null|string
}
```

## Properties
| Name | Description |
| :--- | :---------- |
| [__Type&nbsp;::&nbsp;$line__](entities-Type-var_line.md) | The line number of where this entity begins |
| [__Type&nbsp;::&nbsp;$pos__](entities-Type-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Type&nbsp;::&nbsp;\_\_construct__](entities-Type-__construct.md) | Construct a new Name |
| [__Type&nbsp;::&nbsp;isNative__](entities-Type-isNative.md) | Whether this type is a native type |
| [__Type&nbsp;::&nbsp;getLabel__](entities-Type-getLabel.md) | Get the label for this name  This returns a basename without any namespace part |
| [__Type&nbsp;::&nbsp;getName__](entities-Type-getName.md) | Get the string representation of this name path  This will always return the complete named path with any namespace |
| [__Type&nbsp;::&nbsp;getNamespace__](entities-Type-getNamespace.md) | Get the namespace of this name path |
