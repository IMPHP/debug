# IMPHP - Debug
___

Debugging a project in PHP is not very easy, compared to the debugging done in things like a `Java` project. The biggest problem with PHP, which is also some of the performance benefits, is that most errors will be hidden away until the exact moment that you try to execute it. Even though you may have executed that exact file or even method/function a 100 times before, successfully.

__Example__

```php
use namespace\SomeClasss;

function someTest() {
    try {
        // Something that cold raise exceptions

    } catch (Exception $e) {
        throw new SomeClass( $e->getMessage() );
    }
}
```

The code above could perform perfectly 1000 times, so long as an exception is never raised in the code being executed. But, as soon as an exception is raised and the code tries to invoke `SomeClass`, it will fail. Why? Because it will be trying to use `\SomeClass` rather than `\namespace\SomeClass` because of the extra `s` that was added in the `use` clause. We are including `SomeClassS` and not `SomeClass`. However PHP will never know about this small mistake until the exact moment that it tries to use it. This is a feature in PHP and not a mistake, because without allowing this, things like autoloading would either not be available or it would auto-include every single class file that was being used, even if they are not needed.

But regardless of why PHP is working the way it is, it still raises a lot of problems when building projects. The above example is just one of many small and common mistakes that can linger in code for ages, before finally breaking a system after it has been published. One could test this during Unit Testing, but even such tests can miss a problem or two. The more debugging done, the more issues will be found.

This package was built for internal use cases when working on IMPHP, to catch the most common issues:

 - General code syntax error.
 - Validate enherited datatypes.
 - Validate all datatypes being used in a file.
 - Check `use` clauses to see if they are even used in the code.
 - ...

This package does not contain the entire test suides used by IMPHP, because those are specific to those projects. But it does include the main tools powering them.

### Full Documentation

You can view the [Full Documentation](docs/debug.md) to lean more about what this offers.

### Installation

__Using .phar library__

```sh
wget https://github.com/IMPHP/debug/releases/download/<version>/imphp-debug.phar
```

```php
require "imphp-debug.phar";

...
```

__Clone via git__

```sh
git clone https://github.com/IMPHP/debug.git imphp/debug/
```

__Composer _(Packagist)___

```sh
composer require imphp/debug
```
