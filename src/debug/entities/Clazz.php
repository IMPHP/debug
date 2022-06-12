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
 * Defines a Class _(class, interface, trait or interface)_
 */
class Clazz extends Entity implements IteratorAggregate {

    /**
     * This is a Class
     */
    const T_CLASS = 0x01;               // (0000 0000 0000 0001)

    /**
     * This is an Interface
     */
    const T_INTERFACE = 0x05;           // (0000 0000 0000 0101)

    /**
     * This is a Trait class
     */
    const T_TRAIT = 0x03;               // (0000 0000 0000 0011)

    /**
     * This is an Enum class
     */
    const T_ENUM = 0x09;                // (0000 0000 0000 1001)

    /**
     * This class is Final
     */
    const T_FINAL = 0x0100;             // (0000 0001 0000 0000)

    /**
     * This class is Abstract
     */
    const T_ABSTRACT = 0x0200;          // (0000 0010 0000 0000)

    /**
     * This class is Anonymous
     */
    const T_ANONYMOUS = 0x1000;         // (0001 0000 0000 0000)

    /** @ignore */
    protected array $traits = [];            // ['label' => Name::class, 'label' => Name::class, ...]

    /** @ignore */
    protected array $properties = [];        // ['name' => Property::class, ...]

    /** @ignore */
    protected array $methods = [];           // ['name' => Routine::class, ...]

    /**
     * Construct a new Class
     *
     * @param $name
     *      The name of the clas
     *
     * @param $flags
     *      Class flags
     *
     * @param $extends
     *      A parent class name
     *
     * @param $implements
     *      An array of interface names being used by this class
     */
    public function __construct(
            /** @ignore */
            protected Name $name,

            /** @ignore */
            protected int $flags = Clazz::T_CLASS,

            /** @ignore */
            protected Name|null $extends = null,

            /** @ignore */
            protected array $implements = []

    ) {}

    /**
     * @ignore
     * @php
     */
    public function __toString(): string {
        return (string) $this->getName();
    }

    /**
     * Whether this class is anonymous
     */
    public function isAnonymous(): bool {
        return ($this->flags & static::T_ANONYMOUS) == static::T_ANONYMOUS;
    }

    /**
     * Whether this class is final
     */
    public function isFinal(): bool {
        return ($this->flags & static::T_FINAL) == static::T_FINAL;
    }

    /**
     * Whether this class is abstract
     */
    public function isAbstract(): bool {
        return ($this->flags & static::T_ABSTRACT) == static::T_ABSTRACT;
    }

    /**
     * Whether this class is a trait class
     */
    public function isTrait(): bool {
        return ($this->flags & static::T_TRAIT) == static::T_TRAIT;
    }

    /**
     * Whether this class is an interface
     */
    public function isInterface(): bool {
        return ($this->flags & static::T_INTERFACE) == static::T_INTERFACE;
    }

    /**
     * Whether this class is an enum class
     */
    public function isEnum(): bool {
        return ($this->flags & static::T_ENUM) == static::T_ENUM;
    }

    /**
     * Get the class name
     */
    public function getName(): Name {
        return $this->name;
    }

    /**
     * Get the parent class name
     */
    public function getExtends(): Name|null {
        return $this->extends;
    }

    /**
     * Get a specified interface name
     *
     * @param $label
     *      Name of the interface to return
     */
     public function getImplement(string $label): Name|null {
         foreach ($this->implements as $impl) {
             if ($label == $impl->getName()) {
                 return $impl;
             }
         }

         return null;
     }

    /**
     * Return all interface names used by this class
     */
    public function getImplements(): array {
        return $this->implements;
    }

    /**
     * Add a trait name to this class
     *
     * @note
     *      Trying to add an already existing trait will result in an exception
     *
     * @param $name
     *      A trait name to add
     */
    public function addTrait(Name $name): void {
        $label = $name->getLabel();

        if (!isset($this->traits[$label])) {
            $this->traits[$label] = $name;

        } else {
            throw new Exception("Trying to re-add trait " . $name);
        }
    }

    /**
     * Get a specified trait name
     *
     * @param $label
     *      Name of the trait to return
     */
     public function getTrait(string $label): ?Name {
         return $this->traits[$label] ?? null;
     }

    /**
     * Return all trait names used by this class
     */
    public function getTraits(): array {
        return $this->traits;
    }

    /**
     * Add a property to this class
     *
     * @note
     *      Trying to add an already existing property will result in an exception
     *
     * @param $prop
     *      A property to add
     */
    public function addProperty(Property $prop): void {
        $name = (string) $prop->getName();

        if (!isset($this->properties[$name])) {
            $this->properties[$name] = $prop;

        } else {
            throw new Exception("Trying to re-add property " . $prop);
        }
    }

    /**
     * Get a specified property
     *
     * @param $name
     *      Name of the property to return
     */
     public function getProperty(string $name): ?Property {
         return $this->properties[$name] ?? null;
     }

    /**
     * Return all properties within by this class
     */
    public function getProperties(): array {
        return $this->properties;
    }

    /**
     * Add a method to this class
     *
     * @note
     *      Trying to add an already existing method will result in an exception
     *
     * @param $func
     *      A method to add
     */
    public function addMethod(Routine $func): void {
        $name = (string) $func->getName();

        if (!isset($this->methods[$name])) {
            $this->methods[$name] = $func;

        } else {
            throw new Exception("Trying to re-add routine " . $func);
        }
    }

    /**
     * Get a specified method
     *
     * @param $name
     *      Name of the method to return
     */
     public function getMethod(string $name): ?Routine {
         return $this->methods[$name] ?? null;
     }

    /**
     * Return all methods within by this class
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * Provides a Traversable to iterate through all members
     */
    public function getIterator(): Traversable {  // yield Member::class
        foreach (["properties", "methods"] as $type) {
            foreach ($this->$type as $name => $member) {
                if (!$member->isAnonymous()) {
                    yield $name => $member;
                }
            }
        }
    }
}
