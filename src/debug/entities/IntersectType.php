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
 * Defines a Type container for use with intersection types
 */
class IntersectType extends Type implements IteratorAggregate {

    /** @ignore */
    protected array $types;

    /**
     * Construct a new IntersectType
     *
     * @param $types
     *      Types to add to this intersection
     */
    public function __construct(Type ...$types) {
        $names = [];

        foreach ($types as $type) {
            $names[] = $type->getName();

            if ($type->isNative()) {
                throw new Exception("Cannot use native types in intersection");
            }
        }

        if (count($names) == 0) {
            throw new Exception("You must declare at least one type");
        }

        $this->name = implode("&", $names);
        $this->types = $types;
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
