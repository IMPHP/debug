# [Debug](debug.md) / Debugger
 > im\debug\Debugger
____

## Description
Provides tools to debug code files

This class has a few tools to do basic debug checks on a file.
This is a great way to catch a lot of the basic errors like missing
imports, type error in class names, syntax error etc.

This does not replace a proper Unit Testing setup, but it can be useful
for fixing basic issues before running additional tests.

## Synopsis
```php
final class Debugger {

    // Constants
    public E_INTERNAL = -1
    public E_WARNING = 501
    public E_ERROR = 500

    // Methods
    public static compileClassMap(string $path, bool $fullPath = FALSE): array
    public addSourceTree(string $path): void
    public validateSyntax(string $file, bool $strict = FALSE): bool
    public validateImports(string $file): bool
    public trialRun(string $file): array
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__Debugger&nbsp;::&nbsp;E\_INTERNAL__](debug-Debugger-prop_E_INTERNAL.md) |  |
| [__Debugger&nbsp;::&nbsp;E\_WARNING__](debug-Debugger-prop_E_WARNING.md) |  |
| [__Debugger&nbsp;::&nbsp;E\_ERROR__](debug-Debugger-prop_E_ERROR.md) |  |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Debugger&nbsp;::&nbsp;compileClassMap__](debug-Debugger-compileClassMap.md) | Scan a directory and build a class map  This method will build a complete class map from a directory |
| [__Debugger&nbsp;::&nbsp;addSourceTree__](debug-Debugger-addSourceTree.md) | Add a source directory  This will be used to resolve dependencies when doing things like `trialRun` and such |
| [__Debugger&nbsp;::&nbsp;validateSyntax__](debug-Debugger-validateSyntax.md) | Run a lint test and validate the code syntax |
| [__Debugger&nbsp;::&nbsp;validateImports__](debug-Debugger-validateImports.md) | Validate class imports  This method will report any missing or unused imports |
| [__Debugger&nbsp;::&nbsp;trialRun__](debug-Debugger-trialRun.md) | Run a file in a sandboxed process  This will setup an autoloader using the source trees within this instance and run a script in a sub-process |
