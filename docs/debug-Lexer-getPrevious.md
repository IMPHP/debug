# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: getPrevious
 > im\debug\Lexer
____

## Description
Retreat the pointer and return the previous token, ignoring all ignorable tokens.

 > This is the same as calling `moveToPrevious` followed by `getCurrent`.  

## Synopsis
```php
public getPrevious(array|string|int|null $kind = NULL, array|string|int $stopAt = ' '): PhpToken
```

## Parameters
| Name | Description |
| :--- | :---------- |
| kind | Keep moving until it reaches a token matching $kind. |
| stopAt | When defining $kind, this will define a different stop point rather than EOF. |

## Return
Returns a `T_NULL` token on failure.
