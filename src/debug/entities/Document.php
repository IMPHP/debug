<?php declare(strict_types=1);
/*
 * This file is part of the IMPHP Project: https://github.com/IMPHP
 *
 * Copyright (c) 2018 Daniel Bergløv, License: MIT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace im\debug\entities;

/**
 * Defines a document (global scope) of a php file
 */
interface Document {

    /**
     * Whether or not this document has an PHP open tag
     *
     * This is a way to check if a file is actually
     * a PHP file. 
     */
    function hasOpenTag(): bool;

    /**
     * Whether or not this is a `strict_types` document
     *
     * This will return `TRUE` if it has `strict_types` set
     * in the beginning.
     */
    function isStrict(): bool;

    /**
     * Returns the specified encoding, if any
     *
     * This will return the encoding defined in the beginning,
     * or `NULL` if this is not defined.
     */
    function isDeclaredEncoding(): ?string;

    /**
     * Returns the namespace of this document
     *
     * If this file has a namespace set, it will be returned.
     * Otherwise `NULL` is returned.
     */
    function getNamespace(): string|null;

    /**
     * Find and return a specific class
     *
     * @param $label
     *      Name of the class to find
     */
    function getClass(string $label): Clazz|null;

    /**
     * Returns all classes in this document
     *
     * @note
     *      This includes all anonymous classes within this scope
     */
    function getClasses(): array;       // [Clazz::class, ...]

    /**
     * Find and return a specific function
     *
     * @param $label
     *      Name of the function to find
     */
    function getFunction(string $label): Routine|null;

    /**
     * Returns all functions in this document
     *
     * @note
     *      This includes all anonymous functions/closures within this scope
     */
    function getFunctions(): array;     // [Routine::class, ...]

    /**
     * Find and return a specific import
     *
     * @param $label
     *      Name of the import to find
     */
    function getImport(string $label): ?Import;     // [Import::class, ...]

    /**
     * Returns all imports in this document
     */
    function getImports(): array;   // [Name::class, ...]

    /**
     * Find and return a specific constant
     *
     * @param $label
     *      Name of the constant to find
     */
    function getConstant(string $label): ?Property;

    /**
     * Returns all constants in this document
     */
    function getConstants(): array;     // [Property::class, ...]
}
