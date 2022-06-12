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
 * Defines a class member
 *
 * This is used for things like a method,
 * property, enum case etc.
 */
abstract class Member extends Entity {

    /**
     * This is a public member
     */
    const T_PUBLIC = 0x2000;            // (0000 0010 0000 0000 0000)

    /**
     * This is a protected member
     */
    const T_PROTECTED = 0x4000;         // (0000 0100 0000 0000 0000)

    /**
     * This is a private member
     */
    const T_PRIVATE = 0x8000;           // (0000 1000 0000 0000 0000)

    /**
     * This is a static member
     */
    const T_STATIC = 0x0400;            // (0000 0000 0100 0000 0000)

    /**
     * This is a final member
     */
    const T_FINAL = 0x0100;             // (0000 0000 0001 0000 0000)

    /**
     * Meta that defines all member modifier flags
     */
    const T_META = 0xFFF00;             // (1111 1111 1111 0000 0000)

    /**
     * Construct a new Member
     *
     * @param $name
     *      Name of the member
     *
     * @param $type
     *      The member type
     *
     * @param $flags
     *      Member flags
     */
    public function __construct(
            /** @ignore */
            protected Name $name,

            /** @ignore */
            protected Type $type,

            /** @ignore */
            protected int $flags = Member::T_PUBLIC)
    {}

    /**
     * @ignore
     * @php
     */
    public function __toString(): string {
        return (string) $this->getName();
    }

    /**
     * Get the type of this member
     */
    public function getType(): Type {
        return $this->type;
    }

    /**
     * Get the name of this member
     */
    public function getName(): Name {
        return $this->name;
    }

    /**
     * Whether this member is public
     */
    public function isPublic(): bool {
        return ($this->flags & Member::T_PUBLIC) == Member::T_PUBLIC;
    }

    /**
     * Whether this member is protected
     */
    public function isProtected(): bool {
        return ($this->flags & Member::T_PROTECTED) == Member::T_PROTECTED;
    }

    /**
     * Whether this member is private
     */
    public function isPrivate(): bool {
        return ($this->flags & Member::T_PRIVATE) == Member::T_PRIVATE;
    }

    /**
     * Whether this member is static
     */
    public function isStatic(): bool {
        return ($this->flags & Member::T_STATIC) == Member::T_STATIC;
    }

    /**
     * Whether this member is final
     */
    public function isFinal(): bool {
        return ($this->flags & Member::T_FINAL) == Member::T_FINAL;
    }
}
