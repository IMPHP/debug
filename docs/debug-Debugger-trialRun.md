# [Debug](debug.md) / [Debugger](debug-Debugger.md) :: trialRun
 > im\debug\Debugger
____

## Description
Run a file in a sandboxed process

This will setup an autoloader using the source trees
within this instance and run a script in a sub-process.

If no error occure, the method will return the output
as an array where each element represent a line of the output.

## Synopsis
```php
public trialRun(string $file): array
```

## Parameters
| Name | Description |
| :--- | :---------- |
| file | The file to run |
