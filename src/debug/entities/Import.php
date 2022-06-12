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
 * Defines an import
 *
 * Imports are references added to a document
 * like `use namespace\MyClass as MyAlias`.
 */
class Import extends Name {

    /** @ignore */
    protected int $type;

    /**
     * Construct a new Import
     *
     * @param $path
     *      Complete name path, including possible namespace
     *
     * @param $alias
     *      An alias for this name
     *
     * @param $type
     *      Import type like `function` or `const`.
     *      This defaults to `class`.
     */
    public function __construct(string $path, string|null $alias = null, string|null $type = null) {
        parent::__construct($path, $alias);

        if ($type != null) {
            $type = strtolower($type);
        }

        $this->type = match ($type) {
            "const" => 2,
            "function" => 1,

            default => 0
        };
    }

    /**
     * Get the import type
     */
    public function getType(): string {
        return match ($this->type) {
            0 => "class",
            1 => "function",
            2 => "const"
        };
    }
}
