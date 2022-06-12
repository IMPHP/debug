# [Debug](debug.md) / [Entities](entities.md) / Document
 > im\debug\entities\Document
____

## Description
Defines a document (global scope) of a php file

## Synopsis
```php
interface Document {

    // Methods
    hasOpenTag(): bool
    isStrict(): bool
    isDeclaredEncoding(): null|string
    getNamespace(): null|string
    getClass(string $label): null|im\debug\entities\Clazz
    getClasses(): array
    getFunction(string $label): null|im\debug\entities\Routine
    getFunctions(): array
    getImport(string $label): null|im\debug\entities\Import
    getImports(): array
    getConstant(string $label): null|im\debug\entities\Property
    getConstants(): array
}
```

## Methods
| Name | Description |
| :--- | :---------- |
| [__Document&nbsp;::&nbsp;hasOpenTag__](entities-Document-hasOpenTag.md) | Whether or not this document has an PHP open tag  This is a way to check if a file is actually a PHP file |
| [__Document&nbsp;::&nbsp;isStrict__](entities-Document-isStrict.md) | Whether or not this is a `strict_types` document  This will return `TRUE` if it has `strict_types` set in the beginning |
| [__Document&nbsp;::&nbsp;isDeclaredEncoding__](entities-Document-isDeclaredEncoding.md) | Returns the specified encoding, if any  This will return the encoding defined in the beginning, or `NULL` if this is not defined |
| [__Document&nbsp;::&nbsp;getNamespace__](entities-Document-getNamespace.md) | Returns the namespace of this document  If this file has a namespace set, it will be returned |
| [__Document&nbsp;::&nbsp;getClass__](entities-Document-getClass.md) | Find and return a specific class |
| [__Document&nbsp;::&nbsp;getClasses__](entities-Document-getClasses.md) | Returns all classes in this document |
| [__Document&nbsp;::&nbsp;getFunction__](entities-Document-getFunction.md) | Find and return a specific function |
| [__Document&nbsp;::&nbsp;getFunctions__](entities-Document-getFunctions.md) | Returns all functions in this document |
| [__Document&nbsp;::&nbsp;getImport__](entities-Document-getImport.md) | Find and return a specific import |
| [__Document&nbsp;::&nbsp;getImports__](entities-Document-getImports.md) | Returns all imports in this document |
| [__Document&nbsp;::&nbsp;getConstant__](entities-Document-getConstant.md) | Find and return a specific constant |
| [__Document&nbsp;::&nbsp;getConstants__](entities-Document-getConstants.md) | Returns all constants in this document |
