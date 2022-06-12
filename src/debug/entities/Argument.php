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
 * Defines a function argument
 */
class Argument extends Entity {

    /**
     * This is a byref argument
     */
    const T_BYREF = 0x20;           // (0000 0000 0010 0000)

    /**
     * This is a variable length argument
     */
    const T_VARIADIC = 0x40;        // (0000 0000 0100 0000)

    /**
     * This is an optional (has a default value) argument
     */
    const T_OPTIONAL = 0x80;        // (0000 0000 1000 0000)

    /**
     * Construct a new Argument
     *
     * @param $name
     *      Name of the argument
     *
     * @param $type
     *      The argument type
     *
     * @param $flags
     *      Argument flags
     */
    public function __construct(
            /** @ignore */
            protected string $name,

            /** @ignore */
            protected Type $type,

            /** @ignore */
            protected int $flags = 0)
    {}

    /**
     * @ignore
     * @php
     */
    public function __toString(): string {
        return $this->getName();
    }

    /**
     * Get the argument name
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get the argument type
     */
    public function getType(): Type {
        return $this->type;
    }

    /**
     * Whether this is a nullable argument
     */
    public function isNullable(): bool {
        if ($this->type instanceof UnionType) {
            return $this->type->hasNativeNull();
        }

        return false;
    }

    /**
     * Whether this is an optional argument
     *
     * An optional argument is one that provides a default value
     */
    public function isOptional(): bool {
        return ($this->flags & T_OPTIONAL) > 0;
    }

    /**
     * Whether this argument is passed by reference
     */
    public function isByRef(): bool {
        return ($this->flags & T_BYREF) > 0;
    }

    /**
     * Whether this is a variable length argument
     */
    public function isVariadic(): bool {
        return ($this->flags & T_VARIADIC) > 0;
    }
}
