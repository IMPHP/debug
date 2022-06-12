# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: getPreviousIf
 > im\debug\Lexer
____

## Description
Only retreat the pointer and return the previous token if it matches.

This is the same as calling `getPrevious`, only it will only retreat and
return the token if it matches $kind. This is equal to combining
`peakPrevious` with `getPrevious`.

## Synopsis
```php
public getPreviousIf(array|string|int $kind): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Match to compare |

## Return
Returns a `T_NULL` token on mismatch.
