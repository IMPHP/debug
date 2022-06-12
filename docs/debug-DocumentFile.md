# [Debug](debug.md) / DocumentFile
 > im\debug\DocumentFile
____

## Description
Extract information from a PHP file

This file will extract information like classes, functions,
and so on, from a PHP file. It's similar to what information you can
get from reflection, but without the need to load/include a file.

The class uses basic tokens to build a complete set of OOP entities of
the file content. Functions, closures, classes, anonymous classes etc.
are nested within one another as they are written in the file. Each type,
e.g. class, function, function parameter, imports and so on, are represented by
it's own object, containing all of it's information.

All type declarations, implements, extends ... are resolved based on the documents namespace
and it's defined imports.

## Synopsis
```php
class DocumentFile implements im\debug\entities\Document {

    // Methods
    public static fromCode(string $code): self
    public static fromResource($stream): self
    public __construct(string $file, bool $onlyHeaders = FALSE)
    public hasOpenTag(): bool
    public isStrict(): bool
    public isDeclaredEncoding(): null|string
    public getNamespace(): null|string
    public getReferences(): array
    public getConstant(string $name): null|im\debug\entities\Property
    public getConstants(): array
    public getImport(string $name): null|im\debug\entities\Import
    public getImports(): array
    public getClass(string $name): null|im\debug\entities\Clazz
    public getClasses(): array
    public getFunction(string $label): null|im\debug\entities\Routine
    public getFunctions(): array
}
```

## Methods
| Name | Description |
| :--- | :---------- |
| [__DocumentFile&nbsp;::&nbsp;fromCode__](debug-DocumentFile-fromCode.md) | Return an instance from a code string |
| [__DocumentFile&nbsp;::&nbsp;fromResource__](debug-DocumentFile-fromResource.md) | Return an instance from a code resource |
| [__DocumentFile&nbsp;::&nbsp;\_\_construct__](debug-DocumentFile-__construct.md) | Create a new DocumentFile |
| [__DocumentFile&nbsp;::&nbsp;hasOpenTag__](debug-DocumentFile-hasOpenTag.md) |  |
| [__DocumentFile&nbsp;::&nbsp;isStrict__](debug-DocumentFile-isStrict.md) |  |
| [__DocumentFile&nbsp;::&nbsp;isDeclaredEncoding__](debug-DocumentFile-isDeclaredEncoding.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getNamespace__](debug-DocumentFile-getNamespace.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getReferences__](debug-DocumentFile-getReferences.md) | Get a list of all class references  A class reference is every single class access made |
| [__DocumentFile&nbsp;::&nbsp;getConstant__](debug-DocumentFile-getConstant.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getConstants__](debug-DocumentFile-getConstants.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getImport__](debug-DocumentFile-getImport.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getImports__](debug-DocumentFile-getImports.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getClass__](debug-DocumentFile-getClass.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getClasses__](debug-DocumentFile-getClasses.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getFunction__](debug-DocumentFile-getFunction.md) |  |
| [__DocumentFile&nbsp;::&nbsp;getFunctions__](debug-DocumentFile-getFunctions.md) |  |
