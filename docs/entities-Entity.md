# [Debug](debug.md) / [Entities](entities.md) / Entity
 > im\debug\entities\Entity
____

## Description
Defines an Entity

This interface is shared across all entities like
Argument, Clazz, Routine, Name, Type and more...

## Synopsis
```php
abstract class Entity implements Stringable {

    // Properties
    public int $line = -1
    public int $pos = -1

    // Inherited Methods
    abstract public __toString(): string
}
```

## Properties
| Name | Description |
| :--- | :---------- |
| [__Entity&nbsp;::&nbsp;$line__](entities-Entity-var_line.md) | The line number of where this entity begins |
| [__Entity&nbsp;::&nbsp;$pos__](entities-Entity-var_pos.md) | The position of where this entity begins |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Entity&nbsp;::&nbsp;\_\_toString__](entities-Entity-__toString.md) |  |
