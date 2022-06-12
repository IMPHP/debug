# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: getNextIf
 > im\debug\Lexer
____

## Description
Only advance the pointer and return the next token if it matches.

This is the same as calling `getNext`, only it will only advance and
return the token if it matches $kind. This is equal to combining
`peakNext` with `getNext`.

## Synopsis
```php
public getNextIf(array|string|int $kind): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Match to compare |

## Return
Returns a `T_NULL` token on mismatch.
