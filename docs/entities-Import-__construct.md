# [Entities](entities.md) / [Import](entities-Import.md) :: __construct
 > im\debug\entities\Import
____

## Description
Construct a new Import

## Synopsis
```php
public __construct(string $path, null|string $alias = NULL, null|string $type = NULL)
```

## Parameters
| Name | Description |
| :--- | :---------- |
| path | Complete name path, including possible namespace |
| alias | An alias for this name |
| type | Import type like `function` or `const`.<br />This defaults to `class`. |
