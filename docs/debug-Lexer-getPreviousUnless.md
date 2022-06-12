# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: getPreviousUnless
 > im\debug\Lexer
____

## Description
Only retreat the pointer and return the previous token if it does not match.

This is the same as calling `getPrevious`, only it will only retreat and
return the token if it's a mismatch with $kind.

## Synopsis
```php
public getPreviousUnless(array|string|int $kind): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Match to compare |

## Return
Returns a `T_NULL` token on match.
