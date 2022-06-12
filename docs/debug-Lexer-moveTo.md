# [Debug](debug.md) / [Lexer](debug-Lexer.md) :: moveTo
 > im\debug\Lexer
____

## Description
To a raw move to a specific offset

Unlike `moveToNext` and `moveToPrevious`, this method
does not take ignorable tokens into consideration.
It will simply move the pointer to the specified offset.

This method works will when paired with `getOffset`.

## Synopsis
```php
public moveTo(int $offset, int $whence = im\debug\Lexer::OFFSET_SET): bool
```

## Parameters
| Name | Description |
| :--- | :---------- |
| offset | The offset to move to |
| whence | How to apply the offset, based on the `OFFSET_<X>` constants |
