# [Debug](debug.md) / [Debugger](debug-Debugger.md) :: validateSyntax
 > im\debug\Debugger
____

## Description
Run a lint test and validate the code syntax.

This is a lightweigth version of `trialRun()`.
It's not gonna be as aggressive, but it will catch any
syntax error in the file.

 > This method does not require any additional source tree to work  

## Synopsis
```php
public validateSyntax(string $file, bool $strict = FALSE): bool
```

## Parameters
| Name | Description |
| :--- | :---------- |
| file | The file to check |
