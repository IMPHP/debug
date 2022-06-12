# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: getNextUnless
 > im\debug\Lexer
____

## Description
Only advance the pointer and return the next token if it does not match.

This is the same as calling `getNext`, only it will only advance and
return the token if it's a mismatch with $kind.

## Synopsis
```php
public getNextUnless(array|string|int $kind): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Match to compare |

## Return
Returns a `T_NULL` token on match.
