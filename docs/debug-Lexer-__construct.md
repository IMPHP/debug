# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: __construct
 > im\debug\Lexer
____

## Description
Create a new Lexer instance

__Scan__

The scan option will do the following alterations:

| Original Token                                                     | New Token     |
| ------------------------------------------------------------------ | ------------- |
| T_CLASS (Definition only)                                          | T_CLASSDEF    |
| T_INTERFACE (Definition only)                                      | T_CLASSDEF    |
| T_TRAIT (Definition only)                                          | T_CLASSDEF    |
| T_ENUM (Definition only)                                           | T_CLASSDEF    |
| T_STRING (Following T_CONST in class body)                         | T_PROPERTY    |
| T_STRING (Following T_CASE in class body)                          | T_PROPERTY    |
| T_VARIABLE (In class body)                                         | T_PROPERTY    |
| T_VARIABLE (In function arguments)                                 | T_PARAM       |
| T_NAME_RELATIVE                                                    | T_STRING      |
| T_NAME_QUALIFIED                                                   | T_STRING      |
| T_NAME_FULLY_QUALIFIED                                             | T_STRING      |
| T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG (PHP 8.1)                    | &             |
| T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG (PHP 8.1)                | &             |
| Character '}' belonging to T_CURLY_OPEN                            | T_CURLY_CLOSE |
| ALL typdef tokens, e.g. ?string or int|null ... get's combined     | T_TYPEDEF     |

## Synopsis
```php
public __construct(string $code, bool $scan = TRUE)
```

## Parameters
| Name | Description |
| :--- | :---------- |
| code | Code to tokenize |
| scan | Whether or not to scan the tokens and make alterations. |

## Example 1
```php
function myFunc(?int $arg1): string {}
```

Outputs:
```
T_FUNCTION 'function'
T_STRING 'myFunc'
(
T_TYPEDEF '?int'
T_PARAM 'arg1'
)
:
T_TYPEDEF 'string'
{
}
```
