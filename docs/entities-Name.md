# [Debug](debug.md) / [Entities](entities.md) / Name
 > im\debug\entities\Name
____

## Description
Defines a name with support for alias and namespace

## Synopsis
```php
class Name extends im\debug\entities\Entity implements Stringable {

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(string $path, null|string $alias = NULL)
    public getLabel(): string
    public getName(): string
    public getNamespace(): null|string
}
```

## Properties
| Name | Description |
| :--- | :---------- |
| [__Name&nbsp;::&nbsp;$line__](entities-Name-var_line.md) | The line number of where this entity begins |
| [__Name&nbsp;::&nbsp;$pos__](entities-Name-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Name&nbsp;::&nbsp;\_\_construct__](entities-Name-__construct.md) | Construct a new Name |
| [__Name&nbsp;::&nbsp;getLabel__](entities-Name-getLabel.md) | Get the label for this name  This returns a basename without any namespace part |
| [__Name&nbsp;::&nbsp;getName__](entities-Name-getName.md) | Get the string representation of this name path  This will always return the complete named path with any namespace |
| [__Name&nbsp;::&nbsp;getNamespace__](entities-Name-getNamespace.md) | Get the namespace of this name path |
