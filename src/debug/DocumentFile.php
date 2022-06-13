<?php declare(strict_types=1);
/*
 * This file is part of the IMPHP Project: https://github.com/IMPHP
 *
 * Copyright (c) 2018 Daniel Bergløv, License: MIT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace im\debug;

use Exception;
use im\debug\entities\Document;
use im\debug\entities\Name;
use im\debug\entities\Type;
use im\debug\entities\UnionType;
use im\debug\entities\IntersectType;
use im\debug\entities\Import;
use im\debug\entities\Member;
use im\debug\entities\Clazz;
use im\debug\entities\Property;
use im\debug\entities\Routine;
use im\debug\entities\Argument;

/**
 * Extract information from a PHP file
 *
 * This file will extract information like classes, functions,
 * and so on, from a PHP file. It's similar to what information you can
 * get from reflection, but without the need to load/include a file.
 *
 * The class uses basic tokens to build a complete set of OOP entities of
 * the file content. Functions, closures, classes, anonymous classes etc.
 * are nested within one another as they are written in the file. Each type,
 * e.g. class, function, function parameter, imports and so on, are represented by
 * it's own object, containing all of it's information.
 *
 * All type declarations, implements, extends ... are resolved based on the documents namespace
 * and it's defined imports.
 */
class DocumentFile implements Document {

    /** @ignore */
    protected static array $RADOM_IDS = [];

    /** Used to sort out PHP data types
     *
     * @ignore
     */
    protected static array $TYPE_RESERVED = [];

    /** @ignore */
    protected static array $MODIFIERS = [
        T_FINAL,
        T_ABSTRACT,
        T_PUBLIC,
        T_PROTECTED,
        T_PRIVATE,
        T_STATIC,
        T_CONST,
        T_READONLY,
        T_VAR,
        T_CASE
    ];

    /** @ignore */
    protected bool $hasOpenTag = false;

    /** @ignore */
    protected bool $strict = false;

    /** @ignore */
    protected string $encoding = "";

    /** @ignore */
    protected ?string $namespace = null;

    /** @ignore */
    protected array $imports = [];            // ['label' => Import::class, ...]

    /** @ignore */
    protected array $classes = [];           // ['label' => Clazz::class, ...]

    /** @ignore */
    protected array $routines = [];

    /** @ignore */
    protected array $consts = [];           // ['label' => Property::class, ...]

    /** @ignore */
    protected array $references = [];           // ['label' => Name::class, ...]

    /**
     * Return an instance from a code string
     *
     * @param $code
     *      Code to process, incl. PHP open tags
     */
    public static function fromCode(string $code): self {
        $lexer = new Lexer($code, Lexer::F_SCAN|Lexer::F_COMMENTS);
        $doc = new class() extends DocumentFile {
            public function __construct() {}
        };
        $doc->compile($lexer);

        return $doc;
    }

    /**
     * Return an instance from a code resource
     *
     * @param $stream
     *      A resource with code to process
     */
    public static function fromResource(/*resource*/ $stream): self {
        if (!is_resource($stream) || get_resource_type($stream) != "stream") {
            throw new Exception("You must supply a valid resource of type 'stream'");
        }

        $lexer = new Lexer(stream_get_contents($stream), Lexer::F_SCAN|Lexer::F_COMMENTS);
        $doc = new class() extends DocumentFile {
            public function __construct() {}
        };
        $doc->compile($lexer);

        return $doc;
    }

    /**
     * @internal
     */
    public static function __static_construct() {
        $types = [
            "self", "static", "parent", "class",                                // Non-real class words that can be seen as an object instantiation or static call
            "string", "scalar", "int", "bool", "float", "array",                // Current and possible future native declaration types
            "number", "callable", "object", "resource", "numeric",
            "void", "iterable", "mixed", "null"
        ];

        foreach ($types as $type) {
            static::$TYPE_RESERVED[$type] = new Type($type, null, true);
        }
    }

    /**
     * Create a new DocumentFile
     *
     * @param $file
     *      Path to a php file
     *
     * @param $onlyHeaders
     *      Only extract headers like openTag and define information
     */
    public function __construct(string $file, bool $onlyHeaders = false) {
        if (!is_file($file)) {
            throw new Exception("The file '$file' does not exist");
        }

        $this->compile(
            new Lexer(file_get_contents($file), Lexer::F_SCAN|Lexer::F_COMMENTS),
            $onlyHeaders
        );
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function hasOpenTag(): bool {
        return $this->hasOpenTag;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function isStrict(): bool {
        return $this->strict;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function isDeclaredEncoding(): ?string {
        return empty($this->encoding) ? null : $this->encoding;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getNamespace(): ?string {
        return $this->namespace;
    }

    /**
     * Get a list of all class references
     *
     * A class reference is every single class access made.
     * For an example accessing a static property or constant,
     * creating a new class instance, implementing an interface,
     * extending a base class etc.
     *
     * This method returns a list of all classes being used within
     * this file.
     */
    public function getReferences(): array {
        /*
         * TODO:
         *      Extend this to global constants and functions
         */
        return $this->references;
    }

    /**
     * @internal
     */
    public function addConstant(Property $prop): void {
        $name = (string) $prop->getName();

        if (!isset($this->consts[$name])) {
            $this->consts[$name] = $prop;

        } else {
            throw new Exception("Trying to re-add constant " . $prop);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getConstant(string $name): ?Property {
        return $this->consts[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getConstants(): array {
        return $this->consts;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getImport(string $name): ?Import {
        return $this->imports[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getImports(): array {
        return $this->imports;
    }

    /**
     * @internal
     */
    public function addClass(Clazz $class): void {
        $label = $class->getName()->getLabel();

        if (!isset($this->classes[$label])) {
            $this->classes[$label] = $class;

        } else {
            throw new Exception("Trying to re-add inner class " . $class->getName());
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getClass(string $name): ?Clazz {
        return $this->classes[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getClasses(): array {
        return $this->classes;
    }

    /**
     * @internal
     */
    public function addFunction(Routine $func): void {
        $label = $func->getName()->getLabel();

        if (!isset($this->routines[$label])) {
            $this->routines[$label] = $func;

        } else {
            throw new Exception("Trying to re-add function " . $func->getName());
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getFunction(string $label): ?Routine {
        return $this->routines[$label] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\debug\entities\Document")]
    public function getFunctions(): array {
        return $this->routines;
    }

    /**
     * @internal
     */
    protected function randomId(): string {
        $i = 2;

        while (true) {
            $id = bin2hex(random_bytes($i));

            /* Make sure we don't get conflict.
             * We start with 2 bytes and move up if we
             * encounter conflicts.
             */
            if (!isset(static::$RADOM_IDS[$id])) {
                static::$RADOM_IDS[$id] = true;
                return $id;
            }

            $i++;
        }
    }

    /**
     * @internal
     */
    protected function resolvePathName(string $class, int $line = -1, int $pos = -1): Type {
        if (!isset(static::$TYPE_RESERVED[$class])) {
            switch (true) {
                default:
                    if (strpos($class, "\\") !== false) {
                        $type = new Type(trim($class, "\\")); break;
                    }

                    foreach ($this->imports as $import) {
                        if ($import->getLabel() == $class) {
                            $type = new Type($import->getName(), $class); break 2;
                        }
                    }

                    if (!empty($this->namespace)) {
                        $type = new Type("{$this->namespace}\\{$class}"); break;
                    }

                    $type = new Type($class);
            }

            $this->references[ $type->getLabel() ] = $type;

        } else {
            $type = clone static::$TYPE_RESERVED[$class];
        }

        $type->line = $line;
        $type->pos = $pos;

        return $type;
    }

    /**
     * @internal
     */
    protected function resolveType(string $type, int $line = -1, int $pos = -1): Type {
        if ($type[0] == "?") {
            return new UnionType(
                $this->resolvePathName(substr($type, 1), $line, $pos),
                $this->resolvePathName("null", $line, $pos)
            );

        } else if (strpos($type, "|") !== false) {
            $types = [];

            foreach (explode("|", $type) as $type) {
                $types[] = $this->resolvePathName($type, $line, $pos);
            }

            $type = new UnionType(...$types);
            $type->line = $line;
            $type->pos = $pos;

            return $type;

        } else if (strpos($type, "&") !== false) {
            $types = [];

            foreach (explode("&", $type) as $type) {
                $types[] = $this->resolvePathName($type, $line, $pos);
            }

            $type = new IntersectType(...$types);
            $type->line = $line;
            $type->pos = $pos;

            return $type;

        } else {
            return $this->resolvePathName($type, $line, $pos);
        }
    }

    /**
     * @internal
     */
     protected function resolveModifierFlags(array|string $modifiers): int {
         $flags = 0;

         if (is_string($modifiers)) {
             $modifiers = [$modifiers];
         }

         foreach ($modifiers as $modifier) {
             switch ($modifier) {
                 case "var":
                 case "public":      $flags |= Member::T_PUBLIC; break;
                 case "protected":   $flags |= Member::T_PROTECTED; break;
                 case "private":     $flags |= Member::T_PRIVATE; break;
                 case "static":      $flags |= Member::T_STATIC; break;
                 case "final":       $flags |= Member::T_FINAL; break;
                 case "readonly":    $flags |= Property::T_READONLY; break;
                 case "abstract":    $flags |= Routine::T_ABSTRACT; break;

                 /* A class constant is just a final property, although PHP treats them as different entities
                  */
                 case "case":        $flags |= (Member::T_FINAL|Member::T_STATIC|Property::T_READONLY|Property::T_CASE); break;
                 case "const":       $flags |= (Member::T_FINAL|Member::T_STATIC|Property::T_READONLY);
             }
         }

         return $flags;
     }

    /**
     * @internal
     */
    protected function getLeadingModifiers(Lexer $lexer): array {
        $offset = $lexer->getOffset();
        $modifiers = [];

        while (($token = $lexer->getPrevious())->is(static::$MODIFIERS)) {
            $modifiers[] = $token->text;
        }

        $lexer->moveTo($offset);

        return array_reverse($modifiers);
    }

    /**
     * @internal
     */
    protected function getLeadingDocBlock(Lexer $lexer): string|null {
        $offset = $lexer->getOffset();
        $docblock = null;

        # DocBlocs comes before modifiers
        while (($token = $lexer->getPrevious())->is(static::$MODIFIERS)) {}

        if ($token->is(T_DOC_COMMENT)) {
            $docblock = $token->text;
        }

        $lexer->moveTo($offset);

        return $docblock;
    }

    /**
     * @internal
     */
    protected function compile(Lexer $lexer, bool $headers = false): void {
        $parents = [$this];
        $blocks = [];
        $pending = null;

        /*
         * Make sure that this has PHP code
         */
        while ($lexer->moveTo(1, Lexer::OFFSET_CUR)) {
            $token = $lexer->getCurrent();

            if ($token->getTokenName() == "T_OPEN_TAG") {
                $this->hasOpenTag = true; break;
            }
        }

        /*
         * Only detect declare statements that affects the entire document
         */
        if ($lexer->peakNext()->is(T_DECLARE)) {
            while (!($token = $lexer->getNext())->is([";", "\0"])) {
                switch ($token->getTokenName()) {
                    case "T_STRING":
                        if ($token->is("strict_types") && $lexer->getNext(T_LNUMBER)->is("1")) {
                            $this->strict = true;

                        } else if ($token->is("encoding")) {
                            $this->encoding = $lexer->getNext(T_CONSTANT_ENCAPSED_STRING)->text;
                        }
                }
            }
        }

        // Only extract headers
        if ($headers || !$this->hasOpenTag) {
            return;
        }

        while (!($token = $lexer->getNext())->is("\0")) {
            switch ($token->getTokenName()) {
                case "{":
                        if ($pending !== null) {
                            $blocks[] = $pending;
                            $pending = null;

                        } else {
                            $blocks[] = $token;
                        }

                        break;

                case "}":
                        $block = array_pop($blocks);

                        if (!empty($block) && !$block->is("{")) {
                            array_pop($parents);
                        }

                        break;

                case ";":
                        if ($pending !== null) {
                            $pending = null;
                        }

                        break;

                case "T_FUNCTION":
                        /*
                         * TODO:
                         *      Add support for arrow functions
                         */

                        $pending = $token;
                        $byref = false;
                        $anon = false;
                        $typedef = null;
                        $flags = 0;
                        $args = [];
                        $docblock = $this->getLeadingDocBlock($lexer);
                        $modifiers = $this->getLeadingModifiers($lexer);
                        $parent = end($parents);
                        $subDocs = null;

                        if (!$lexer->getNextIf("&")->is("\0")) {
                            $byref = true;
                        }

                        if (($subToken = $lexer->getNext())->is(T_STRING)) { // Anonymous functions would be '(' here
                            $name = $subToken->text;

                        } else {
                            $name = "closure#{$this->randomId()}";
                            $anon = true;
                        }

                        if ($parent instanceof self) {
                            if (($ns = $parent->getNamespace()) != null) {
                                $name = new Name("{$ns}\\{$name}");

                            } else {
                                $name = new Name($name);
                            }

                        } else {
                            $name = new Name("{$parent->getName()}::{$name}", $name);
                        }

                        while (!($subToken = $lexer->getNextUnless([";", "{"]))->is("\0")) {
                            switch ($subToken->getTokenName()) {
                                case "T_DOC_COMMENT":
                                        $subDocs = $subToken->text;

                                        break;

                                case "T_PUBLIC":
                                case "T_PROTECTED":
                                case "T_PRIVATE":
                                case "T_READONLY":
                                        if ($parent instanceof Clazz) {
                                            $flags |= $this->resolveModifierFlags($subToken->text);
                                        }

                                        break;

                                case "&":
                                        $flags |= Argument::T_BYREF;

                                        break;

                                case "T_ELLIPSIS":
                                        $flags |= Argument::T_VARIADIC;

                                        break;

                                case "T_TYPEDEF": // This will at some point be the return type
                                        $typedef = $subToken->text;

                                        break;

                                case "T_PARAM":
                                        $flags |= $lexer->peakNext()->is("=") ? Argument::T_OPTIONAL : 0;

                                        if (!empty($typedef)) {
                                            $typedef = $this->resolveType($typedef, $token->line, $token->pos);

                                        } else {
                                            $typedef = $this->resolveType("mixed", $token->line, $token->pos);
                                        }

                                        if (($flags & Member::T_META) > 0) {
                                            $pname = new Name("{$parent->getName()}::{$subToken->text}", $subToken->text);
                                            $prop = new Property(
                                                $pname,
                                                $typedef,
                                                $flags & Member::T_META
                                            );

                                            $prop->line = $token->line;
                                            $prop->pos = $token->pos;

                                            if ($subDocs != null) {
                                                $prop->setDocBlock($subDocs);
                                            }

                                            $parent->addProperty($prop);
                                        }

                                        $args[] = new Argument($subToken->text, $typedef, $flags & ~Member::T_META);
                                        $typedef = null;
                                        $subDocs = null;
                                        $flags = 0;
                            }
                        }

                        if (!empty($typedef)) {
                            $typedef = $this->resolveType($typedef, $token->line, $token->pos);

                        } else {
                            $typedef = $this->resolveType("mixed", $token->line, $token->pos);
                        }

                        $flags = $this->resolveModifierFlags($modifiers);

                        if ($byref) {
                            $flags |= Routine::T_BYREF;
                        }

                        if ($anon) {
                            $flags |= Routine::T_ANONYMOUS;
                        }

                        if ($parent instanceof Clazz && $parent->isInterface()) {
                            $flags |= Routine::T_ABSTRACT;
                        }

                        $func = new Routine($name, $typedef, $flags, $args);
                        $func->line = $token->line;
                        $func->pos = $token->pos;

                        if ($docblock != null) {
                            $func->setDocBlock($docblock);
                        }

                        if ($parent instanceof Clazz) {
                            $parent->addMethod($func);

                        } else {
                            $parent->addFunction($func);
                        }

                        $parents[] = $func;

                        break;

                case "T_CLASSDEF":
                        $pending = $token;
                        $name = null;
                        $extends = null;
                        $implements = [];
                        $docblock = $this->getLeadingDocBlock($lexer);
                        $modifiers = $this->getLeadingModifiers($lexer);
                        $flags = $this->resolveModifierFlags($modifiers);
                        $flags |= Clazz::T_CLASS;

                        if ($lexer->peakPrevious()->is(T_NEW) && $lexer->peakNext()->is("(")) {
                            // Skip arguments passed to anonymous class
                            while (!$lexer->getNext()->is(")")) {}
                        }

                        while (!($subToken = $lexer->getNextUnless([";", "{"]))->is("\0")) {
                            switch (($tname = $subToken->getTokenName())) {
                                case "T_STRING":
                                        $name = $subToken->text;

                                        break;

                                case "T_IMPLEMENTS":
                                case "T_EXTENDS":
                                        do {
                                            $class = $this->resolvePathName($lexer->getNext()->text, $token->line, $token->pos);

                                            if ($tname == "T_IMPLEMENTS" || $token->text == "interface") {
                                                $implements[] = $class;

                                            } else {
                                                $extends = $class;
                                            }

                                        } while (!($subToken = $lexer->getNextIf(","))->is("\0"));
                            }
                        }

                        if (empty($name)) {
                            $name = "class#{$this->randomId()}";
                            $flags |= Clazz::T_ANONYMOUS;
                        }

                        if (!empty($this->namespace)) {
                            $name = "{$this->namespace}\\{$name}";
                        }

                        if ($token->text == "interface") {
                            $flags |= Clazz::T_INTERFACE;

                        } else if ($token->text == "trait") {
                            $flags |= Clazz::T_TRAIT;
                        }

                        $class = new Clazz(new Name($name), $flags, $extends, $implements);
                        $class->line = $token->line;
                        $class->pos = $token->pos;

                        if ($docblock != null) {
                            $class->setDocBlock($docblock);
                        }

                        $parent = end($parents);
                        $parent->addClass($class);

                        $parents[] = $class;

                        break;

                case "T_NAMESPACE":
                        if ($this->namespace !== null) {
                            /* ClassFile represents a single namespace
                             * so we break out here.
                             */
                            break 2;
                        }

                        $this->namespace = $lexer->getNext()->text;

                        break;

                case "T_USE":
                        if (count($blocks) == 0) {
                            $namespace = "";
                            $alias = "";

                            while (!($subToken = $lexer->getNextUnless(";"))->is("\0")) {
                                switch ($subToken->getTokenName()) {
                                    case "T_CONST":
                                    case "T_FUNCTION":
                                            $type = $subToken->text; // E.g. 'use <const/function> ...'

                                            break;

                                    case "T_STRING":
                                            $namespace = $subToken->text;

                                            break;

                                    case "T_AS":
                                            $alias = $lexer->getNext(T_STRING)->text;

                                            break;

                                    case "{":
                                            while (!($subToken = $lexer->getNextUnless("}"))->is("\0")) {
                                                switch ($subToken->getTokenName()) {
                                                    case "T_STRING":
                                                            $subNamespace = $subToken->text;
                                                            $subAlias = null;

                                                            if ($lexer->peakNext()->is(T_AS)) {
                                                                $subAlias = $lexer->getNext(T_STRING)->text;
                                                            }

                                                            $import = new Import("{$namespace}\\{$subNamespace}", $subAlias, $type ?? null);
                                                            $import->line = $token->line;
                                                            $import->pos = $token->pos;

                                                            $this->imports[ $import->getLabel() . ($type ?? null !== null ? ":{$type}" : "") ] = $import;
                                                }
                                            }

                                            $namespace = null;

                                            break;
                                }
                            }

                            if (!empty($namespace)) {
                                $import = new Import($namespace, $alias, $type ?? null);
                                $import->line = $token->line;
                                $import->pos = $token->pos;

                                $this->imports[ $import->getLabel() . ($type ?? null !== null ? ":{$type}" : "") ] = $import;
                            }

                        } else {
                            $parent = end($parents);

                            if ($parent instanceof Clazz) {
                                while (!($subToken = $lexer->getNextUnless(["{", ";"]))->is("\0")) {
                                    switch ($subToken->getTokenName()) {
                                        /*
                                         * TODO:
                                         *      Add support for Conflict Resolution and Method Visibility
                                         */
                                        case "T_STRING":
                                                $parent->addTrait(
                                                    $this->resolvePathName($subToken->text, $token->line, $token->pos)
                                                );
                                    }
                                }
                            }
                        }

                        break;

                case "T_PROPERTY":
                        $typedef = "mixed";

                        if ($lexer->peakPrevious()->is(T_TYPEDEF)) {
                            $offset = $lexer->getOffset();
                            $typedef = $lexer->getPrevious()->text;
                            $docblock = $this->getLeadingDocBlock($lexer);
                            $modifiers = $this->getLeadingModifiers($lexer);
                            $lexer->moveTo($offset);

                        } else {
                            if ($lexer->peakPrevious()->is(T_CASE)) {
                                $typedef = "object";
                            }

                            $docblock = $this->getLeadingDocBlock($lexer);
                            $modifiers = $this->getLeadingModifiers($lexer);
                        }

                        $flags = $this->resolveModifierFlags($modifiers);
                        $parent = end($parents);
                        $name = "{$parent->getName()}::{$token->text}";
                        $label = $token->text;

                        $prop = new Property(
                                    new Name($name, $label),
                                    $this->resolveType($typedef, $token->line, $token->pos),
                                    $flags
                                );

                        $prop->line = $token->line;
                        $prop->pos = $token->pos;

                        if ($docblock != null) {
                            $prop->setDocBlock($docblock);
                        }

                        $parent->addProperty($prop);

                        break;

                case "T_CONST":
                        $parent = end($parents);

                        if ($parent instanceof self) {
                            $name = $lexer->getNext()->text;
                            $docblock = $this->getLeadingDocBlock($lexer);
                            $modifiers = $this->getLeadingModifiers($lexer);
                            $flags = $this->resolveModifierFlags($modifiers);

                            if (($ns = $parent->getNamespace()) != null) {
                                $name = "{$ns}\\{$name}";
                            }

                            $prop = new Property(
                                        new Name($name),
                                        $this->resolveType("mixed", $token->line, $token->pos),
                                        $flags
                                    );

                            $prop->line = $token->line;
                            $prop->pos = $token->pos;

                            if ($docblock != null) {
                                $prop->setDocBlock($docblock);
                            }

                            $parent->addConstant($prop);
                        }

                        break;

                /*
                 * Find remaining references.
                 * This is not a complex search, but will catch
                 * most if not all of the common stuff.
                 */
                case "T_INSTANCEOF":
                case "T_CATCH":
                case "T_NEW":
                        if (!($subToken = $lexer->peakNext(T_STRING, ["(", ";"]))->is("\0")) {
                            $this->resolvePathName($subToken->text, $token->line, $token->pos);
                        }

                        break;

                case "T_DOUBLE_COLON":
                        if (($subToken = $lexer->peakPrevious())->is(T_STRING)) {
                            $this->resolvePathName($subToken->text, $token->line, $token->pos);
                        }
            }
        }
    }
}

DocumentFile::__static_construct();
