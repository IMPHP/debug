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
 * Defines a Routine (function)
 *
 * This can be anything from a closure to a class method
 * or a regular function.
 */
class Routine extends Member implements IteratorAggregate {

    /**
     * This is a byref function return
     */
    const T_BYREF = 0x20;               // (0000 0000 0000 0010 0000)

    /**
     * This is an abstract member
     */
    const T_ABSTRACT = 0x0200;          // (0000 0000 0010 0000 0000)

    /**
     * This is anonymous member
     */
    const T_ANONYMOUS = 0x1000;         // (0000 0001 0000 0000 0000)

    /** @ignore */
    protected array $params = [];               // ['name' => Argument::class, ...]

    /** @ignore */
    protected array $classes = [];              // ['name' => Clazz::class, ...]

    /** @ignore */
    protected array $routines = [];             // ['name' => Routine::class, ...]

    /** @ignore */
    protected int $flags = 0;

    /**
     * Construct a new Routine
     *
     * @param $name
     *      The name of the routine
     *
     * @param $type
     *      The routine return type
     *
     * @param $flags
     *      Routine flags
     *
     * @param $params
     *      Routine parameters
     */
    public function __construct(Name $name, Type $type, int $flags = Member::T_PUBLIC, array $params = []) {
        parent::__construct($name, $type, $flags);

        foreach ($params as $param) {
            if ($param instanceof Argument) {
                $this->params[$param->getName()] = $param;

            } else {
                throw new Exception("The params parameter must be populated with instances of ". Argument::class);
            }
        }
    }

    /**
     *
     */
    #[Override("im\debug\entities\Entity")]
    public function getSynopsis(): string {
        $args = [[]];

        foreach ($this->params as $name => $param) {
            $pos = count($args)-1;

            if ($param->isOptional()) {
                $args[$pos+1] = $args[$pos++];
            }

            $args[$pos][] = $param->getSynopsis();
        }

        $flags = [
            static::T_PUBLIC,
            static::T_PROTECTED,
            static::T_PRIVATE,
            static::T_FINAL,
            static::T_ABSTRACT,
            static::T_STATIC
        ];
        $mods = [];

        foreach ($flags as $flag) {
            $mod = match (($this->flags & $flag) == $flag ? $flag : 0) {
                static::T_PUBLIC => "public",
                static::T_PROTECTED => "protected",
                static::T_PRIVATE => "private",
                static::T_FINAL => "final",
                static::T_ABSTRACT => "abstract",
                static::T_STATIC => "static",

                default => null
            };

            if ($mod != null) {
                $mods[] = $mod;
            }
        }

        $def = trim(implode(" ", $mods) . " function");

        if (!$this->isAnonymous()) {
            $def .= " {$this->name->getLabel()}";
        }

        $syn = "";
        foreach ($args as $arg) {
            $syn .= "$def(" . implode(", ", $arg) . "): {$this->type->getSynopsis()}\n";
        }

        return trim($syn);
    }

    /**
     * Whether this routine returns by reference
     */
    public function isByRef(): bool {
        return ($this->flags & Routine::T_BYREF) == Routine::T_BYREF;
    }

    /**
     * Whether this member is abstract
     */
    public function isAbstract(): bool {
        return ($this->flags & Routine::T_ABSTRACT) == Routine::T_ABSTRACT;
    }

    /**
     * Whether this member is anonymous
     */
    public function isAnonymous(): bool {
        return ($this->flags & Routine::T_ANONYMOUS) == Routine::T_ANONYMOUS;
    }

    /**
     * Get a specified argument
     *
     * @param $name
     *      Name of the argument
     */
    public function getArgument(string $name): ?Argument {
        return $this->params[$name] ?? null;
    }

    /**
     * Provides a Traversable to iterate through all arguments
     */
    public function getIterator(): Traversable {  // yield Argument::class
        foreach ($this->params as $name => $param) {
            yield $name => $param;
        }
    }

    /**
     * Add an anonymous class
     *
     * @param $class
     *      The class to add
     */
    public function addClass(Clazz $class): void {
        $label = $class->getName()->getLabel();

        if (!isset($this->classes[$label])) {
            $this->classes[$label] = $class;

        } else {
            throw new Exception("Trying to re-add inner class " . $class->getName());
        }
    }

    /**
     * Return all of the anonymous classes within this routine
     */
    public function getClasses(): array {
        return $this->classes;
    }

    /**
     * Add a closure
     *
     * @param $func
     *      The closure to add
     */
    public function addFunction(Routine $func): void {
        $label = $func->getName()->getLabel();

        if (!isset($this->routines[$label])) {
            $this->routines[$label] = $func;

        } else {
            throw new Exception("Trying to re-add function " . $func->getName());
        }
    }

    /**
     * Return all of the closures within this routine
     */
    public function getFunctions(): array {
        return $this->routines;
    }
}
