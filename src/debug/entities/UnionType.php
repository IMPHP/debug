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

use Exception;
use IteratorAggregate;
use Traversable;

/**
 * Defines a Type container for use with union types
 */
class UnionType extends Type implements IteratorAggregate {

    /** @ignore */
    protected array $natives = [];

    /** @ignore */
    protected array $types;

    /**
     * Construct a new UnionType
     *
     * @param $types
     *      Types to add to this union
     */
    public function __construct(Type ...$types) {
        $names = [];
        $isNative = true;

        foreach ($types as $type) {
            $names[] = $type->getName();

            if (!$type->isNative()) {
                $isNative = false;

            } else {
                $this->natives[] = $type->getName();
            }
        }

        if (count($names) == 0) {
            throw new Exception("You must declare at least one type");
        }

        $this->name = implode("|", $names);
        $this->native = $isNative;
        $this->types = $types;
    }

    /**
     * Check to see if a specific or any native types exists
     * within this union
     *
     * @param $type
     *      Optional native type to check for.
     *      If this is not provided, any native type will return `TRUE`
     */
    public function hasNative(string|null $type = null): bool {
        if ($type == null) {
            return count($this->natives) > 0;
        }

        return in_array($type, $this->natives);
    }

    /**
     * Check to see if this union is nullable
     *
     * @note
     *      This is the same as `hasNative("null")`
     */
    public function hasNativeNull(): bool {
        return $this->hasNative("null");
    }

    /**
     * Provides a Traversable to iterate through the types within
     */
    public function getIterator(): Traversable {  // yield Type::class
        foreach ($this->types as $type) {
            yield $type;
        }
    }
}
