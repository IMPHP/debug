# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: peakPrevious
 > im\debug\Lexer
____

## Description
Return the previous token without retreating the pointer, ignoring all ignorable tokens.

## Synopsis
```php
public peakPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Keep moving until it reaches a token matching $kind. |
| stopAt | When defining $kind, this will define a different stop point rather than EOF. |

## Return
Returns a `T_NULL` token on failure.
