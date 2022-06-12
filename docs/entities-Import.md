# [Debug](debug.md) / [Entities](entities.md) / Import
 > im\debug\entities\Import
____

## Description
Defines an import

Imports are references added to a document
like `use namespace\MyClass as MyAlias`.

## Synopsis
```php
class Import extends im\debug\entities\Name implements Stringable {

    // Inherited Properties
    public int $line = -1
    public int $pos = -1

    // Methods
    public __construct(string $path, null|string $alias = NULL, null|string $type = NULL)
    public getType(): string

    // Inherited Methods
    public getLabel(): string
    public getName(): string
    public getNamespace(): null|string
}
```

## Properties
| Name | Description |
| :--- | :---------- |
| [__Import&nbsp;::&nbsp;$line__](entities-Import-var_line.md) | The line number of where this entity begins |
| [__Import&nbsp;::&nbsp;$pos__](entities-Import-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Import&nbsp;::&nbsp;\_\_construct__](entities-Import-__construct.md) | Construct a new Import |
| [__Import&nbsp;::&nbsp;getType__](entities-Import-getType.md) | Get the import type |
| [__Import&nbsp;::&nbsp;getLabel__](entities-Import-getLabel.md) | Get the label for this name  This returns a basename without any namespace part |
| [__Import&nbsp;::&nbsp;getName__](entities-Import-getName.md) | Get the string representation of this name path  This will always return the complete named path with any namespace |
| [__Import&nbsp;::&nbsp;getNamespace__](entities-Import-getNamespace.md) | Get the namespace of this name path |
