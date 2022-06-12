# [Debug](debug.md) / [Debugger](debug-Debugger.md) :: compileClassMap
 > im\debug\Debugger
____

## Description
Scan a directory and build a class map

This method will build a complete class map from a directory.
It will scan each file and extract any class information from it,
and add it's path to the map.

## Synopsis
```php
public static compileClassMap(string $path, bool $fullPath = FALSE): array
```

## Parameters
| Name | Description |
| :--- | :---------- |
| path | The directory to scan |
| fullPath | Add complete paths to the class map.<br />Otyherwise the paths will be relative to $path |

## Return
An assoc array with class names as key and file paths as value
