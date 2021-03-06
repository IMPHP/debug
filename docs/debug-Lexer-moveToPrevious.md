# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: moveToPrevious
 > im\debug\Lexer
____

## Description
Move pointer to the previous token, ignoring all ignorable tokens.

## Synopsis
```php
public moveToPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): bool
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Keep moving until it reaches a token matching $kind.<br />If BOF is reached, the pointer will not advance and `FALSE` is retured. |
| stopAt | When defining $kind, this will define a different stop point rather than EOF. |

## Return
Returns `FALSE` on `BOF` or `TRUE` on success
