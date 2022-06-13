<?php declare(strict_types=1);
/*
 * This file is part of the IMPHP Project: https://github.com/IMPHP
 *
 * Copyright (c) 2022 Daniel Bergløv, License: MIT
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
namespace im\debug;

use PhpToken;
use IteratorAggregate;
use Traversable;

define("T_NULL", 0);

$i = 100000;
$constants = [
    // Lexer specific
    "T_TYPEDEF",
    "T_CLASSDEF",
    "T_PARAM",
    "T_PROPERTY",

    // PHP 8.1
    "T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG",
    "T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG",
    "T_READONLY",
    "T_ENUM",

    // PHP Inconsistentcy
    "T_CURLY_CLOSE"
];

foreach ($constants as $constant) {
    if (!defined($constant)) {
        define($constant, $i++);
    }
}

unset($constants);
unset($i);

/**
 * A lexer built on PHP's token system
 *
 * It's great that PHP has a way to print tokens from code text.
 * It makes it easier to create parsers, as PHP's own interpreter does some
 * of the initial work. But, there is very litle consistency in these tokens.
 *
 * Example:
 *      Make a return type from a function with the type `namespace\MyClass`
 *      and you will get a 'T_NAME_QUALIFIED'.
 *
 *      Now add a 'use namespace\MyClass' to the top and change the return type
 *      to 'MyClass'. I would expect something like 'T_NAME' or similar, but instead
 *      we get a 'T_STRING'. It makes no sense and now we need to filter out every single
 *      'T_STRING' to find the same thing.
 *
 *      We get the same behaviour with things like 'static' vs 'self'.
 *      Make a typedef of 'static' and you get 'T_STATIC' while 'self'
 *      will provide a 'T_STRING'.
 *
 *      Other types like 'object' vs 'array' have the same behaviour.
 *
 * This lexer fixes some of these inconsistencies while also keeping compatibility
 * between various PHP versions, that often breaks compatibility.
 *
 * At the same time this class provides a nice and clean way of
 * propagating through and working with the tokens.
 */
class Lexer implements IteratorAggregate {

    /**
     *
     */
    public const F_SCAN = 0x01;

    /**
     *
     */
    public const F_COMMENTS = 0x02;

    /**
     * Set position equal to offset bytes
     */
    public const OFFSET_SET = 1;

    /**
     * Set position to current location plus offset
     */
    public const OFFSET_CUR = 2;

    /**
     * Set position to end-of-file plus offset
     */
    public const OFFSET_END = 3;

    /** @ignore */
    protected static ?PhpToken $T_IGNORE;

    /** @ignore */
    protected static ?PhpToken $T_NULL;

    /** @ignore */
    protected static array $DATATYPE_IDS = [
        T_NAME_RELATIVE,
        T_NAME_QUALIFIED,
        T_NAME_FULLY_QUALIFIED,
        T_STRING,
        T_ARRAY,
        T_CALLABLE
    ];

    /** @ignore */
    protected int $flags = 0;

    /** @ignore */
    protected array $tokens;

    /** @ignore */
    protected int $offset = -1;

    /** @ignore */
    protected int $scanned = -1;

    /** @ignore */
    protected bool $scan = false;

    /** @ignore */
    protected int $length = -1;

    /** @ignore */
    protected array $blocks = [];

    /** @ignore */
    protected ?PhpToken $pending = null;

    /**
     * @ignore
     */
    public static function __static_construct() {
        static::$T_IGNORE = new PhpToken(T_WHITESPACE, " ");
        static::$T_NULL = new class(T_NULL, "\0", -1, -1) extends PhpToken {
            public function getTokenName(): ?string {
                return "T_NULL";
            }
        };
    }

    /**
     * Create a new Lexer instance
     *
     * __Scan__
     *
     * The scan option will do the following alterations:
     *
     * | Original Token                                                     | New Token     |
     * | ------------------------------------------------------------------ | ------------- |
     * | T_CLASS (Definition only)                                          | T_CLASSDEF    |
     * | T_INTERFACE (Definition only)                                      | T_CLASSDEF    |
     * | T_TRAIT (Definition only)                                          | T_CLASSDEF    |
     * | T_ENUM (Definition only)                                           | T_CLASSDEF    |
     * | T_STRING (Following T_CONST in class body)                         | T_PROPERTY    |
     * | T_STRING (Following T_CASE in class body)                          | T_PROPERTY    |
     * | T_VARIABLE (In class body)                                         | T_PROPERTY    |
     * | T_VARIABLE (In function arguments)                                 | T_PARAM       |
     * | T_NAME_RELATIVE                                                    | T_STRING      |
     * | T_NAME_QUALIFIED                                                   | T_STRING      |
     * | T_NAME_FULLY_QUALIFIED                                             | T_STRING      |
     * | T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG (PHP 8.1)                    | &             |
     * | T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG (PHP 8.1)                | &             |
     * | Character '}' belonging to T_CURLY_OPEN                            | T_CURLY_CLOSE |
     * | ALL typdef tokens, e.g. ?string or int|null ... get's combined     | T_TYPEDEF     |
     *
     * @example
     *      ```php
     *      function myFunc(?int $arg1): string {}
     *      ```
     *
     *      Outputs:
     *      ```
     *      T_FUNCTION 'function'
     *      T_STRING 'myFunc'
     *      (
     *      T_TYPEDEF '?int'
     *      T_PARAM 'arg1'
     *      )
     *      :
     *      T_TYPEDEF 'string'
     *      {
     *      }
     *      ```
     *
     * @param $code
     *      Code to tokenize
     *
     * @param $scan
     *      Whether or not to scan the tokens and make alterations.
     */
    public function __construct(string $code, int $flags = Lexer::F_SCAN) {
        $this->tokens = PhpToken::tokenize($code);
        $this->length = count($this->tokens);
        $this->flags = $flags;
    }

    /**
     * Scan and modify the token array
     *
     * This method scans the tokens and modifies it.
     * It provides compatibility between PHP version as well as
     * fixing some of the inconsistencies that the token system has.
     *
     * @internal
     */
    protected function scan(int $minOffset = -1): void {
        if ($minOffset <= $this->scanned) {
            return;
        }

        $offset = $this->scanned;
        $break = false;

        while (!$break && ($token = $this->tokens[++$offset] ?? null) !== null) {
            if (!$token->isIgnorable()) {
                switch ($tokenName = $token->getTokenName()) {
                    case "T_CLASS":
                    case "T_INTERFACE":
                    case "T_TRAIT":
                    case "T_ENUM":
                                if (($subToken = $this->tokens[$offset-1] ?? null) === null
                                        || $subToken->getTokenName() != "T_DOUBLE_COLON") {

                                    /*
                                     * Convert all class tokens like T_CLASS, T_TRAIT, T_ENUM and T_INTERFACE into T_CLASSDEF
                                     */
                                    $this->tokens[$offset] = new class(T_CLASSDEF, $token->text, $token->line, $token->pos) extends PhpToken {
                                        public function getTokenName(): ?string {
                                            return "T_CLASSDEF";
                                        }
                                    };

                                    $this->pending = $this->tokens[$offset];
                                }

                                break;

                    case "T_FN":
                    case "T_FUNCTION":
                                $this->pending = $token;

                                break;

                    case "T_CURLY_OPEN":
                                array_push($this->blocks, $token);

                                break;

                    case "}":
                                if (($block = end($this->blocks)) !== false && $block->is(T_CURLY_OPEN)) {
                                    $this->tokens[$offset] = new class(T_CURLY_CLOSE, $token->text, $token->line, $token->pos) extends PhpToken {
                                        public function getTokenName(): ?string {
                                            return "T_CURLY_CLOSE";
                                        }
                                    };
                                }

                                array_pop($this->blocks);

                                break;

                    case "{":
                    case ";":
                    case "T_DOUBLE_ARROW":
                                if (
                                    $tokenName != "T_DOUBLE_ARROW" || ($this->pending !== null && $this->pending->is(T_FN))
                                ) {
                                    $bypass = $this->pending !== null && $this->pending->is([T_FUNCTION, T_FN]);

                                    if ($tokenName != ";") {
                                        if ($this->pending !== null) {
                                            array_push($this->blocks, $this->pending);

                                        } else {
                                            array_push($this->blocks, $token);
                                        }

                                    } else {
                                        while (($block = end($this->blocks)) !== false && $block->is(T_FN)) {
                                            array_pop($this->blocks);
                                        }
                                    }

                                    $this->pending = null;

                                    if ($offset >= $minOffset) {
                                        $break = true;
                                    }
                                }

                                if (!($bypass ?? false)) {
                                    break;
                                }

                    case "T_CONST":
                    case "T_VARIABLE":
                    case "T_CASE":
                                if ($token->is(T_VARIABLE)) {
                                    $token->text = substr($token->text, 1);
                                }

                                /*
                                 * A pending block is the beginning of a definition block,
                                 * like 'class ...' or 'function ...' before
                                 * the beginning '{' block character.
                                 *
                                 * A block (not pending) is when we are within a block body like a class or function.
                                 * It can also be an 'if' statement or similar, which is why we later check the block type.
                                 */
                                if (
                                    $bypass ?? false
                                    ||
                                    (
                                        ($block = $this->pending) !== null && $block->is([T_FUNCTION, T_FN])
                                    )
                                    ||
                                    (
                                        ($block = end($this->blocks)) !== false && $block->is(T_CLASSDEF)
                                    )
                                ) {
                                    $cur = $offset;
                                    $addKind = [];

                                    if ($token->is(T_VARIABLE) && !$block->is(T_CLASSDEF)) {
                                        /*
                                         * Convert T_VARIABLE to T_PARAM in function argument
                                         */
                                        $this->tokens[$cur] = new class(T_PARAM, $token->text, $token->line, $token->pos) extends PhpToken {
                                            public function getTokenName(): ?string {
                                                return "T_PARAM";
                                            }
                                        };

                                        /*
                                         * Rewind the pointer to where the type definition may begin
                                         */
                                        while (($token = $this->tokens[--$cur])->is(["...", "&"]) || $token->isIgnorable()) {}

                                    } else {
                                        if ($bypass ?? false) {
                                            /*
                                             * We are currently extracting a function return type.
                                             * A class method is allowed to return a 'static' type
                                             */
                                            $addKind[] = T_STATIC; // Add additional return type

                                        } else {
                                            /*
                                             * Advance the pointer to where the actual property begins.
                                             */
                                            if ($token->is([T_CONST, T_CASE])) {
                                                while (($token = $this->tokens[++$cur])->isIgnorable()) {} // The actual property comes after
                                            }

                                            /*
                                             * Convert T_VARIABLE or T_STRING to T_PROPERTY in a class body
                                             */
                                            $this->tokens[$cur] = new class(T_PROPERTY, $token->text, $token->line, $token->pos) extends PhpToken {
                                                public function getTokenName(): ?string {
                                                    return "T_PROPERTY";
                                                }
                                            };
                                        }

                                        /*
                                         * Rewind the pointer to where the type definition may begin
                                         */
                                        while (($token = $this->tokens[--$cur])->isIgnorable()) {}
                                    }

                                    $this->convertTypeDefs($cur);
                                }

                                break;

                    case "T_NAME_RELATIVE":
                    case "T_NAME_QUALIFIED":
                    case "T_NAME_FULLY_QUALIFIED":
                                /*
                                 *Since 'ClassName' uses T_STRING it makes no sense for 'namespace\ClassName' not to
                                 */
                                $this->tokens[$offset] = new PhpToken(T_STRING, $token->text, $token->line, $token->pos);

                                break;

                    case "T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG":
                    case "T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG":
                                /*
                                 * Fix compatibility break besteen PHP 8.0 and 8.1+
                                 */
                                $this->tokens[$offset] = new class($token->id, $token->text, $token->line, $token->pos) extends PhpToken {
                                    public function getTokenName(): ?string {
                                        return "&";
                                    }
                                };

                }
            }
        }

        $this->scanned = $offset;
    }

    /**
     * Extract type def information from functions and properties
     *
     * @internal
     */
    private function convertTypeDefs(int $offset, array $addKind = []): void {
        $token = $this->tokens[$offset];

        if ($token->is(static::$DATATYPE_IDS) || $token->is($addKind)) {
            $value = $token->text;
            $line = $token->line;
            $pos = $token->pos;
            $ofs = $offset;

            /*
             * Extract all the typedef tokens
             */
            while (($token = $this->tokens[--$offset])->is(["|", "&"]) || $token->is($addKind)
                    || $token->is(static::$DATATYPE_IDS) || $token->isIgnorable()) {

                if (!$token->isIgnorable()) {
                    $value = $token->text . $value;
                    $this->tokens[$offset] = static::$T_IGNORE;

                    /* Line and position should start at the beginning
                     * of the type definition and we are moving backwards
                     */
                    $line = $token->line;
                    $pos = $token->pos;
                }
            }

            if ($this->tokens[$offset]->is("?")) {
                $value = "?" . $value;
                $this->tokens[$offset] = static::$T_IGNORE;
            }

            /*
             * Convert all of the typedef tokens into a single T_TYPEDEF
             */
            $this->tokens[$ofs] = new class(T_TYPEDEF, $value, $line, $pos) extends PhpToken {
                public function getTokenName(): ?string {
                    return "T_TYPEDEF";
                }
            };
        }
    }

    /**
     *
     */
    public function getIterator(): Traversable {
        while (!($token = $this->getNext())->is("\0")) {
            yield $token;
        }
    }

    /**
     * To a raw move to a specific offset
     *
     * Unlike `moveToNext` and `moveToPrevious`, this method
     * does not take ignorable tokens into consideration.
     * It will simply move the pointer to the specified offset.
     *
     * This method works will when paired with `getOffset`.
     *
     * @param $offset
     *      The offset to move to
     *
     * @param $whence
     *      How to apply the offset, based on the `OFFSET_<X>` constants
     */
    public function moveTo(int $offset, int $whence = Lexer::OFFSET_SET): bool {
        if ($whence != Lexer::OFFSET_SET) {
            if ($whence == Lexer::OFFSET_END) {
                $offset = $this->length + $offset;

            } else {
                $offset = $this->offset + $offset;
            }

            if ($offset < -1 || $offset > $this->length) {
                return false;
            }

        } else if ($offset > $this->length || $offset < -1) {
            return false;
        }

        $this->offset = $offset;

        if ($this->flags & static::F_SCAN && $offset > $this->scanned) {
            $this->scan($offset);
        }

        return true;
    }

    /**
     * Move pointer to the next token, ignoring all ignorable tokens.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *      If EOF is reached, the pointer will not advance and `FALSE` is retured.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns `FALSE` on `EOF` or `TRUE` on success
     */
    public function moveToNext(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): bool {
        $offset = $this->offset;

        do {
            if ($this->flags & static::F_SCAN && $offset >= $this->scanned) {
                $this->scan($offset+1);
            }

            if (($token = $this->tokens[++$offset] ?? null) !== null) {
                if ((!$token->isIgnorable() || ($this->flags & static::F_COMMENTS && $token->is(T_DOC_COMMENT)))
                        && ($kind === null || $token->is($kind))) {

                    $this->offset = $offset;

                    return true;
                }
            }

        } while ($token !== null && !$token->is($stopAt));

        return false;
    }

    /**
     * Move pointer to the previous token, ignoring all ignorable tokens.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *      If BOF is reached, the pointer will not advance and `FALSE` is retured.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns `FALSE` on `BOF` or `TRUE` on success
     */
    public function moveToPrevious(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): bool {
        $offset = $this->offset;

        do {
            if (($token = $this->tokens[--$offset] ?? null) !== null) {
                if ((!$token->isIgnorable() || ($this->flags & static::F_COMMENTS && $token->is(T_DOC_COMMENT)))
                        && ($kind === null || $token->is($kind))) {

                    $this->offset = $offset;

                    return true;
                }
            }

        } while ($token !== null && !$token->is($stopAt));

        return false;
    }

    /**
     * Get the current token being pointed to
     */
    public function getCurrent(): PhpToken {
        if (($token = $this->tokens[$this->offset] ?? null) === null) {
            return static::$T_NULL;
        }

        return $token;
    }

    /**
     * Advance the pointer and return the next token, ignoring all ignorable tokens.
     *
     * @note
     *      This is the same as calling `moveToNext` followed by `getCurrent`.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns a `T_NULL` token on failure.
     */
    public function getNext(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): PhpToken {
        if ($this->moveToNext($kind, $stopAt)) {
            return $this->getCurrent();
        }

        return static::$T_NULL;
    }

    /**
     * Only advance the pointer and return the next token if it matches.
     *
     * This is the same as calling `getNext`, only it will only advance and
     * return the token if it matches $kind. This is equal to combining
     * `peakNext` with `getNext`.
     *
     * @param $kind
     *      Match to compare
     *
     * @return
     *      Returns a `T_NULL` token on mismatch.
     */
    public function getNextIf(int|string|array $kind): PhpToken {
        if ($this->peakNext()->is($kind)) {
            return $this->getNext();
        }

        return static::$T_NULL;
    }

    /**
     * Only advance the pointer and return the next token if it does not match.
     *
     * This is the same as calling `getNext`, only it will only advance and
     * return the token if it's a mismatch with $kind.
     *
     * @param $kind
     *      Match to compare
     *
     * @return
     *      Returns a `T_NULL` token on match.
     */
    public function getNextUnless(int|string|array $kind): PhpToken {
        if (!$this->peakNext()->is($kind)) {
            return $this->getNext();
        }

        return static::$T_NULL;
    }

    /**
     * Return the next token without advancing the pointer, ignoring all ignorable tokens.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns a `T_NULL` token on failure.
     */
    public function peakNext(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): PhpToken {
        $token = static::$T_NULL;
        $offset = $this->getOffset();

        if ($this->moveToNext($kind, $stopAt)) {
            $token = $this->getCurrent();
        }

        $this->moveTo($offset);

        return $token;
    }

    /**
     * Retreat the pointer and return the previous token, ignoring all ignorable tokens.
     *
     * @note
     *      This is the same as calling `moveToPrevious` followed by `getCurrent`.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns a `T_NULL` token on failure.
     */
    public function getPrevious(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): PhpToken {
        if ($this->moveToPrevious($kind, $stopAt)) {
            return $this->getCurrent();
        }

        return static::$T_NULL;
    }

    /**
     * Only retreat the pointer and return the previous token if it matches.
     *
     * This is the same as calling `getPrevious`, only it will only retreat and
     * return the token if it matches $kind. This is equal to combining
     * `peakPrevious` with `getPrevious`.
     *
     * @param $kind
     *      Match to compare
     *
     * @return
     *      Returns a `T_NULL` token on mismatch.
     */
    public function getPreviousIf(int|string|array $kind): PhpToken {
        if ($this->peakPrevious()->is($kind)) {
            return $this->getPrevious();
        }

        return static::$T_NULL;
    }

    /**
     * Only retreat the pointer and return the previous token if it does not match.
     *
     * This is the same as calling `getPrevious`, only it will only retreat and
     * return the token if it's a mismatch with $kind.
     *
     * @param $kind
     *      Match to compare
     *
     * @return
     *      Returns a `T_NULL` token on match.
     */
    public function getPreviousUnless(int|string|array $kind): PhpToken {
        if (!$this->peakPrevious()->is($kind)) {
            return $this->getPrevious();
        }

        return static::$T_NULL;
    }

    /**
     * Return the previous token without retreating the pointer, ignoring all ignorable tokens.
     *
     * @param $kind
     *      Keep moving until it reaches a token matching $kind.
     *
     * @param $stopAt
     *      When defining $kind, this will define a different stop point rather than EOF.
     *
     * @return
     *      Returns a `T_NULL` token on failure.
     */
    public function peakPrevious(int|string|array|null $kind = null, int|string|array $stopAt = "\0"): PhpToken {
        $token = static::$T_NULL;
        $offset = $this->getOffset();

        if ($this->moveToPrevious($kind, $stopAt)) {
            $token = $this->getCurrent();
        }

        $this->moveTo($offset);

        return $token;
    }

    /**
     * Get the current pointer offset
     */
    public function getOffset(): int {
        return $this->offset;
    }
}

Lexer::__static_construct();
