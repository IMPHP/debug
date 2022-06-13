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
 * Defines a property
 *
 * This can be class variable or constant as well as
 * a global constant.
 */
class Property extends Member {

    /**
     * This is a readonly member
     */
    const T_READONLY = 0x0800;          // (0000 0000 1000 0000 0000)

    /**
     * This is an enum case member
     */
    const T_CASE = 0x10000;             // (0001 0000 0000 0000 0000)

    /**
     *
     */
    #[Override("im\debug\entities\Entity")]
    public function getSynopsis(): string {
        $char = "";

        if (($this->flags & static::T_PUBLIC) == static::T_PUBLIC) {
            $mods[] = "public";

        } else if (($this->flags & static::T_PROTECTED) == static::T_PROTECTED) {
            $mods[] = "protected";

        } else if (($this->flags & static::T_PRIVATE) == static::T_PRIVATE) {
            $mods[] = "private";
        }

        if (($this->flags & static::T_FINAL) == static::T_FINAL) {
            $mods[] = "const";

        } else {
            $char = "\$";

            if (($this->flags & static::T_STATIC) == static::T_STATIC) {
                $mods[] = "static";
            }

            if (($this->flags & static::T_READONLY) == static::T_READONLY) {
                $mods[] = "readonly";
            }
        }

        $syn = trim(implode(" ", $mods) . " {$this->type->getSynopsis()} {$char}{$this->name->getLabel()}");

        return $syn;
    }

    /**
     * Whether this member is readonly
     */
    public function isReadonly(): bool {
        return ($this->flags & Property::T_READONLY) == Property::T_READONLY;
    }

    /**
     * Whether this member is an enum case
     */
    public function isEnumCase(): bool {
        return ($this->flags & Property::T_CASE) == Property::T_CASE;
    }
}
