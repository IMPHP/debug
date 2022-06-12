# [Entities](entities.md) / [UnionType](entities-UnionType.md) :: getLabel
 > im\debug\entities\UnionType
____

## Description
Get the label for this name

This returns a basename without any namespace part.
If an alias is defined, this is what get's returned.
Otherwise it will return the name, with namespace stripped.

## Synopsis
```php
public getLabel(): string
```
