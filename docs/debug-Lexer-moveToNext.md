# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: moveToNext
 > im\debug\Lexer
____

## Description
Move pointer to the next token, ignoring all ignorable tokens.

## Synopsis
```php
public moveToNext(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): bool
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Keep moving until it reaches a token matching $kind.<br />If EOF is reached, the pointer will not advance and `FALSE` is retured. |
| stopAt | When defining $kind, this will define a different stop point rather than EOF. |

## Return
Returns `FALSE` on `EOF` or `TRUE` on success
