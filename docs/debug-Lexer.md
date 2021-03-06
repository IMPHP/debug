# [Debug](debug.md) / Lexer
 > im\debug\Lexer
____

## Description
A lexer built on PHP's token system

It's great that PHP has a way to print tokens from code text.
It makes it easier to create parsers, as PHP's own interpreter does some
of the initial work. But, there is very litle consistency in these tokens.

Example:
     Make a return type from a function with the type `namespace\MyClass`
     and you will get a 'T_NAME_QUALIFIED'.

     Now add a 'use namespace\MyClass' to the top and change the return type
     to 'MyClass'. I would expect something like 'T_NAME' or similar, but instead
     we get a 'T_STRING'. It makes no sense and now we need to filter out every single
     'T_STRING' to find the same thing.

     We get the same behaviour with things like 'static' vs 'self'.
     Make a typedef of 'static' and you get 'T_STATIC' while 'self'
     will provide a 'T_STRING'.

     Other types like 'object' vs 'array' have the same behaviour.

This lexer fixes some of these inconsistencies while also keeping compatibility
between various PHP versions, that often breaks compatibility.

At the same time this class provides a nice and clean way of
propagating through and working with the tokens.

## Synopsis
```php
class Lexer implements IteratorAggregate, Traversable {

    // Constants
    public OFFSET_SET = 1
    public OFFSET_CUR = 2
    public OFFSET_END = 3

    // Methods
    public __construct(string $code, bool $scan = TRUE)
    public getIterator(): Traversable
    public moveTo(int $offset, int $whence = im\debug\Lexer::OFFSET_SET): bool
    public moveToNext(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): bool
    public moveToPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): bool
    public getCurrent(): PhpToken
    public getNext(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
    public getNextIf(array|string|int $kind): PhpToken
    public getNextUnless(array|string|int $kind): PhpToken
    public peakNext(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
    public getPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
    public getPreviousIf(array|string|int $kind): PhpToken
    public getPreviousUnless(array|string|int $kind): PhpToken
    public peakPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
    public getOffset(): int
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__Lexer&nbsp;::&nbsp;OFFSET\_SET__](debug-Lexer-prop_OFFSET_SET.md) | Set position equal to offset bytes |
| [__Lexer&nbsp;::&nbsp;OFFSET\_CUR__](debug-Lexer-prop_OFFSET_CUR.md) | Set position to current location plus offset |
| [__Lexer&nbsp;::&nbsp;OFFSET\_END__](debug-Lexer-prop_OFFSET_END.md) | Set position to end-of-file plus offset |

## Methods
| Name | Description |
| :--- | :---------- |
| [__Lexer&nbsp;::&nbsp;\_\_construct__](debug-Lexer-__construct.md) | Create a new Lexer instance  __Scan__  The scan option will do the following alterations:  | Original Token                                                     | New Token     | | ------------------------------------------------------------------ | ------------- | | T_CLASS (Definition only)                                          | T_CLASSDEF    | | T_INTERFACE (Definition only)                                      | T_CLASSDEF    | | T_TRAIT (Definition only)                                          | T_CLASSDEF    | | T_ENUM (Definition only)                                           | T_CLASSDEF    | | T_STRING (Following T_CONST in class body)                         | T_PROPERTY    | | T_STRING (Following T_CASE in class body)                          | T_PROPERTY    | | T_VARIABLE (In class body)                                         | T_PROPERTY    | | T_VARIABLE (In function arguments)                                 | T_PARAM       | | T_NAME_RELATIVE                                                    | T_STRING      | | T_NAME_QUALIFIED                                                   | T_STRING      | | T_NAME_FULLY_QUALIFIED                                             | T_STRING      | | T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG (PHP 8 |
| [__Lexer&nbsp;::&nbsp;getIterator__](debug-Lexer-getIterator.md) |  |
| [__Lexer&nbsp;::&nbsp;moveTo__](debug-Lexer-moveTo.md) | To a raw move to a specific offset  Unlike `moveToNext` and `moveToPrevious`, this method does not take ignorable tokens into consideration |
| [__Lexer&nbsp;::&nbsp;moveToNext__](debug-Lexer-moveToNext.md) | Move pointer to the next token, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;moveToPrevious__](debug-Lexer-moveToPrevious.md) | Move pointer to the previous token, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;getCurrent__](debug-Lexer-getCurrent.md) | Get the current token being pointed to |
| [__Lexer&nbsp;::&nbsp;getNext__](debug-Lexer-getNext.md) | Advance the pointer and return the next token, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;getNextIf__](debug-Lexer-getNextIf.md) | Only advance the pointer and return the next token if it matches |
| [__Lexer&nbsp;::&nbsp;getNextUnless__](debug-Lexer-getNextUnless.md) | Only advance the pointer and return the next token if it does not match |
| [__Lexer&nbsp;::&nbsp;peakNext__](debug-Lexer-peakNext.md) | Return the next token without advancing the pointer, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;getPrevious__](debug-Lexer-getPrevious.md) | Retreat the pointer and return the previous token, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;getPreviousIf__](debug-Lexer-getPreviousIf.md) | Only retreat the pointer and return the previous token if it matches |
| [__Lexer&nbsp;::&nbsp;getPreviousUnless__](debug-Lexer-getPreviousUnless.md) | Only retreat the pointer and return the previous token if it does not match |
| [__Lexer&nbsp;::&nbsp;peakPrevious__](debug-Lexer-peakPrevious.md) | Return the previous token without retreating the pointer, ignoring all ignorable tokens |
| [__Lexer&nbsp;::&nbsp;getOffset__](debug-Lexer-getOffset.md) | Get the current pointer offset |
