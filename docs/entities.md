# [Debug](debug.md) / Entities
____

## Description
Reflection-like package for debugging purposes.

## Interfaces
| Name | Description |
| :--- | :---------- |
| [Document](entities-Document.md) | Defines a document (global scope) of a php file |

## Classes
| Name | Description |
| :--- | :---------- |
| [Argument](entities-Argument.md) | Defines a function argument |
| [Clazz](entities-Clazz.md) | Defines a Class _(class, interface, trait or interface)_ |
| [Entity](entities-Entity.md) | Defines an Entity  This interface is shared across all entities like Argument, Clazz, Routine, Name, Type and more |
| [Import](entities-Import.md) | Defines an import  Imports are references added to a document like `use namespace\MyClass as MyAlias` |
| [IntersectType](entities-IntersectType.md) | Defines a Type container for use with intersection types |
| [Member](entities-Member.md) | Defines a class member  This is used for things like a method, property, enum case etc |
| [Name](entities-Name.md) | Defines a name with support for alias and namespace |
| [Property](entities-Property.md) | Defines a property  This can be class variable or constant as well as a global constant |
| [Routine](entities-Routine.md) | Defines a Routine (function)  This can be anything from a closure to a class method or a regular function |
| [Type](entities-Type.md) | Defines a type  This is an extension to `im\debug\entities\Name` which also supports native (builtin) types |
| [UnionType](entities-UnionType.md) | Defines a Type container for use with union types |
