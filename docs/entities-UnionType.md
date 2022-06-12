# [Debug](debug.md) / [Entities](entities.md) / UnionType
 > im\debug\entities\UnionType
____

## Description
Defines a Type container for use with union types

## Synopsis
```php
class UnionType extends im\debug\entities\Type implements Stringable, IteratorAggregate, Traversable {

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(im\debug\entities\Type ...$types)
    public hasNative(null|string $type = NULL): bool
    public hasNativeNull(): bool
    public getIterator(): Traversable

    // Inherited Methods
    public isNative(): bool
    public getLabel(): string
    public getName(): string
    public getNamespace(): null|string
}
```

## Properties
| Name | Description |
| :--- | :---------- |
| [__UnionType&nbsp;::&nbsp;$line__](entities-UnionType-var_line.md) | The line number of where this entity begins |
| [__UnionType&nbsp;::&nbsp;$pos__](entities-UnionType-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__UnionType&nbsp;::&nbsp;\_\_construct__](entities-UnionType-__construct.md) | Construct a new UnionType |
| [__UnionType&nbsp;::&nbsp;hasNative__](entities-UnionType-hasNative.md) | Check to see if a specific or any native types exists |
| [__UnionType&nbsp;::&nbsp;hasNativeNull__](entities-UnionType-hasNativeNull.md) | Check to see if this union is nullable |
| [__UnionType&nbsp;::&nbsp;getIterator__](entities-UnionType-getIterator.md) | Provides a Traversable to iterate through the types within |
| [__UnionType&nbsp;::&nbsp;isNative__](entities-UnionType-isNative.md) | Whether this type is a native type |
| [__UnionType&nbsp;::&nbsp;getLabel__](entities-UnionType-getLabel.md) | Get the label for this name  This returns a basename without any namespace part |
| [__UnionType&nbsp;::&nbsp;getName__](entities-UnionType-getName.md) | Get the string representation of this name path  This will always return the complete named path with any namespace |
| [__UnionType&nbsp;::&nbsp;getNamespace__](entities-UnionType-getNamespace.md) | Get the namespace of this name path |
