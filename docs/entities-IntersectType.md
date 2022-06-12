# [Debug](debug.md) / [Entities](entities.md) / IntersectType
 > im\debug\entities\IntersectType
____

## Description
Defines a Type container for use with intersection types

## Synopsis
```php
class IntersectType extends im\debug\entities\Type implements Stringable, IteratorAggregate, Traversable {

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(im\debug\entities\Type ...$types)
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
| [__IntersectType&nbsp;::&nbsp;$line__](entities-IntersectType-var_line.md) | The line number of where this entity begins |
| [__IntersectType&nbsp;::&nbsp;$pos__](entities-IntersectType-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__IntersectType&nbsp;::&nbsp;\_\_construct__](entities-IntersectType-__construct.md) | Construct a new IntersectType |
| [__IntersectType&nbsp;::&nbsp;getIterator__](entities-IntersectType-getIterator.md) | Provides a Traversable to iterate through the types within |
| [__IntersectType&nbsp;::&nbsp;isNative__](entities-IntersectType-isNative.md) | Whether this type is a native type |
| [__IntersectType&nbsp;::&nbsp;getLabel__](entities-IntersectType-getLabel.md) | Get the label for this name  This returns a basename without any namespace part |
| [__IntersectType&nbsp;::&nbsp;getName__](entities-IntersectType-getName.md) | Get the string representation of this name path  This will always return the complete named path with any namespace |
| [__IntersectType&nbsp;::&nbsp;getNamespace__](entities-IntersectType-getNamespace.md) | Get the namespace of this name path |
