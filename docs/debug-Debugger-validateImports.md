# [Debug](debug.md) / [Debugger](debug-Debugger.md) :: validateImports
 > im\debug\Debugger
____

## Description
Validate class imports

This method will report any missing or unused imports.
It checks all class references, resolved using the import information
in the file, and looks it up in iether the produced source tree or among
PHP's internal classes.

It will also look at all the defined imports to see if they are actually
being used within the file.

## Synopsis
```php
public validateImports(string $file): bool
```

## Parameters
| Name | Description |
| :--- | :---------- |
| file | The file to check |
