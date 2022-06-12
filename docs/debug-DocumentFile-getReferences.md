# [Debug](debug.md) / [DocumentFile](debug-DocumentFile.md) :: getReferences
 > im\debug\DocumentFile
____

## Description
Get a list of all class references

A class reference is every single class access made. 
For an example accessing a static property or constant, 
creating a new class instance, implementing an interface, 
extending a base class etc. 

This method returns a list of all classes being used within
this file.

## Synopsis
```php
public getReferences(): array
```
