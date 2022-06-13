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
namespace im\test\debug;

use PHPUnit\Framework\TestCase;
use im\debug\Lexer;

/**
 *
 */
final class LexerTest extends TestCase {

    /**
     *
     */
    public function test_scan(): void {
        $lexer = new Lexer("<?php
            namespace lib\\utils;

            class MyClass {
                private ?int \$prop = 0;

                public function &getProp(bool &...\$someVar = false): int|null {
                    return \$this->prop;
                }

                private obj1&obj2 \$prop;
            }

        ", 1);

        $this->assertTrue(
            $lexer->getNext()->is(T_NAMESPACE) && $lexer->getNext()->is(T_STRING)
        );

        $this->assertEquals(
            "lib\\utils",
            (string) $lexer->getCurrent()
        );

        $this->assertTrue(
            $lexer->moveToNext(T_CLASSDEF)
        );

        $this->assertFalse(
            $lexer->moveToNext(T_ELLIPSIS, T_PROPERTY)
        );

        $this->assertEquals(
            "\0",
            (string) $lexer->getNext(T_ELLIPSIS, T_PROPERTY)
        );

        $this->assertEquals(
            "MyClass",
            (string) $lexer->getNext()
        );

        $this->assertTrue(
            $lexer->moveToNext(T_TYPEDEF)
        );

        $this->assertEquals(
            "?int",
            (string) $lexer->getCurrent()
        );

        $this->assertTrue(
            $lexer->getNext()->is(T_PROPERTY)
        );

        $this->assertEquals(
            "prop",
            (string) $lexer->getCurrent()
        );

        $this->assertTrue(
            $lexer->moveToNext(T_ELLIPSIS) && ($token = $lexer->peakPrevious())->is("&")
        );

        $this->assertEquals(
            "&",
            $token->getTokenName()
        );

        $this->assertTrue(
            $lexer->moveToNext(":") && $lexer->peakNext()->is(T_TYPEDEF)
        );

        $this->assertFalse(
            $lexer->moveToPrevious(T_CLASSDEF, T_PROPERTY)
        );

        $this->assertEquals(
            "\0",
            (string) $lexer->getPrevious(T_CLASSDEF, T_PROPERTY)
        );

        $this->assertEquals(
            "int|null",
            (string) $lexer->getNextIf(T_TYPEDEF)
        );

        $this->assertTrue(
            $lexer->moveToNext(T_PRIVATE) && $lexer->getNext()->is(T_TYPEDEF)
        );

        $this->assertEquals(
            "obj1&obj2",
            (string) $lexer->getCurrent()
        );
    }
}
