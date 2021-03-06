# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: peakNext
 > im\debug\Lexer
____

## Description
Return the next token without advancing the pointer, ignoring all ignorable tokens.

## Synopsis
```php
public peakNext(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Keep moving until it reaches a token matching $kind. |
| stopAt | When defining $kind, this will define a different stop point rather than EOF. |

## Return
Returns a `T_NULL` token on failure.
