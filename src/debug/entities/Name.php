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
 * Defines a name with support for alias and namespace
 */
class Name extends Entity {

    /** @ignore */
    protected string $name;

    /** @ignore */
    protected string|null $alias = null;

    /** @ignore */
    protected string|null $namespace = null;

    /**
     * Construct a new Name
     *
     * @param $path
     *      Complete name path, including possible namespace
     *
     * @param $alias
     *      An alias for this name
     */
    public function __construct(string $path, string|null $alias = null) {
        $path = trim($path, "\\");

        if (($pos = strrpos($path, "\\")) !== false) {
            $this->namespace = substr($path, 0, $pos);
            $this->name = substr($path, $pos+1);

        } else {
            $this->name = $path;
        }

        $this->alias = $alias;
    }

    /**
     *
     */
    #[Override("im\debug\entities\Entity")]
    public function getSynopsis(): string {
        return $this->getLabel();
    }

    /**
     * Check whether this name has an alias
     */
    public function hasAlias(): bool {
        return $this->alias != null;
    }

    /**
     * Get the label for this name
     *
     * This returns a basename without any namespace part.
     * If an alias is defined, this is what get's returned.
     * Otherwise it will return the name, with namespace stripped.
     */
    public function getLabel(): string {
        return $this->alias != null ?
                    $this->alias : $this->name;
    }

    /**
     * Get the string representation of this name path
     *
     * This will always return the complete named path
     * with any namespace.
     */
    public function getName(): string {
        if ($this->namespace != null) {
            return "{$this->namespace}\\{$this->name}";
        }

        return $this->name;
    }

    /**
     * Get the namespace of this name path
     */
    public function getNamespace(): string|null {
        return $this->namespace;
    }

    /**
     * @ignore
     * @php
     */
    public function __toString(): string {
        return $this->getName();
    }
}
