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
use ErrorException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Provides tools to debug code files
 *
 * This class has a few tools to do basic debug checks on a file.
 * This is a great way to catch a lot of the basic errors like missing
 * imports, type error in class names, syntax error etc.
 *
 * This does not replace a proper Unit Testing setup, but it can be useful
 * for fixing basic issues before running additional tests.
 */
final class Debugger {

    /**
     *
     */
    public const E_INTERNAL = -1;

    /**
     *
     */
    public const E_WARNING = 501;

    /**
     *
     */
    public const E_ERROR = 500;

    /** @ignore */
    private object $cache;

    /** @ignore */
    private array $classmap = [];

    /**
     * Scan a directory and build a class map
     *
     * This method will build a complete class map from a directory.
     * It will scan each file and extract any class information from it,
     * and add it's path to the map.
     *
     * @param $path
     *      The directory to scan
     *
     * @param $fullPath
     *      Add complete paths to the class map.
     *      Otyherwise the paths will be relative to $path
     *
     * @return
     *      An assoc array with class names as key and file paths as value 
     */
    public static function compileClassMap(string $path, bool $fullPath = false): array {
        if (!is_dir($path)) {
            $path = dirname($path);

            if (!is_dir($path)) {
                throw new Exception("Cannot create class map, path '$path' does not exist", Debugger::E_INTERNAL);
            }
        }

        $map = [];
        $path = realpath($path);
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                // Directories are not class files
                continue;
            }

            $doc = new DocumentFile("$file");
            $classes = $doc->getClasses();
            $filePath = $fullPath ? $file->getPathName() : substr($file->getPathName(), strlen($path)+1);

            foreach ($classes as $class) {
                if (!$class->isAnonymous()) {
                    $map["$class"] = $filePath;
                }
            }
        }

        return $map;
    }

    /**
     * Add a source directory
     *
     * This will be used to resolve dependencies when doing things
     * like `trialRun` and such.
     *
     * @param $path
     *      Directory path with additional dependencies
     */
    public function addSourceTree(string $path): void {
        $map = Debugger::compileClassMap($path, true);

        foreach ($map as $class => $file) {
            $this->classmap[$class] = $file;
        }
    }

    /**
     * Run a lint test and validate the code syntax.
     *
     * This is a lightweigth version of `trialRun()`.
     * It's not gonna be as aggressive, but it will catch any
     * syntax error in the file.
     *
     * @note
     *      This method does not require any additional source tree to work
     *
     * @param $file
     *      The file to check
     */
    public function validateSyntax(string $file, bool $strict = false): bool {
        if (!is_file($file)) {
            throw new Exception("Failed to validate syntax, the file $file does not exist", Debugger::E_INTERNAL);
        }

        $output = [];
        $result = 0;

        exec("php -l '$file' 2>&1", $output, $result);

        if ($result != 0) {
            $msg = trim(substr($output[0], strpos($output[0], ":")+1));

            if (preg_match("/ in (?<file>.+) on line (?<line>[0-9]+)$/", $msg, $match)) {
                $msg = preg_replace("/ in .+ on line [0-9]+$/", "", $msg);
                $file = $match["file"];
                $line = (int) $match["line"];

                throw new ErrorException($msg, Debugger::E_ERROR, E_ERROR, $file, $line);
            }

            throw new Exception(implode("\n", $output), Debugger::E_ERROR);

        } else if ($strict) {
            $doc = new DocumentFile($file, true);

            if (!$doc->isStrict()) {
                throw new ErrorException("File does not declare strict types", Debugger::E_WARNING, E_WARNING, $file, 1);
            }
        }

        return true;
    }

    /**
     * Validate class imports
     *
     * This method will report any missing or unused imports.
     * It checks all class references, resolved using the import information
     * in the file, and looks it up in iether the produced source tree or among
     * PHP's internal classes.
     *
     * It will also look at all the defined imports to see if they are actually
     * being used within the file.
     *
     * @param $file
     *      The file to check
     */
    public function validateImports(string $file): bool {
        if (!is_file($file)) {
            throw new Exception("Failed to validate imports, the file $file does not exist", Debugger::E_INTERNAL);
        }

        $doc = new DocumentFile($file);

        if ($doc->hasOpenTag()) {
            $refs = $doc->getReferences();

            foreach ($refs as $ref) {
                if (!isset($this->classmap["$ref"]) && !class_exists("$ref", true) && !interface_exists("$ref", true) && !trait_exists("$ref", true) && !enum_exists("$ref", true)) {
                    throw new ErrorException("Cannot locate class $ref", Debugger::E_ERROR, E_WARNING, $file, $ref->line);
                }
            }

            $imports = $doc->getImports();

            foreach ($imports as $label => $import) {
                if ($import->getType() == "class" && !isset($refs[$label])) {
                    throw new ErrorException("The import $import is never used", Debugger::E_WARNING, E_WARNING, $file, $import->line);
                }
            }
        }

        return true;
    }

    /**
     * Run a file in a sandboxed process
     *
     * This will setup an autoloader using the source trees
     * within this instance and run a script in a sub-process.
     *
     * If no error occure, the method will return the output
     * as an array where each element represent a line of the output.
     *
     * @param $file
     *      The file to run
     */
    public function trialRun(string $file): array {
        if (!is_file($file)) {
            throw new Exception("Failed to do a trial run, the file $file does not exist", Debugger::E_INTERNAL);
        }

        $output = [];
        $result = 0;

        exec("php -r '{$this->createBootstrap($file)}' 2>&1", $output, $result);

        if ($result != 0) {
            foreach ($output as $line) {
                $msg = trim(substr($line, strpos($line, ":")+1));

                if (preg_match("/ in (?<file>.+)(?:\:| on line )(?<line>[0-9]+)$/", $msg, $match)) {
                    $msg = preg_replace("/ in .+(\:| on line )[0-9]+$/", "", $msg);
                    $file = $match["file"];
                    $lineno = (int) $match["line"] ?? -1;

                    throw new ErrorException($msg, Debugger::E_ERROR, E_ERROR, $file, $lineno);
                }
            }

            throw new Exception(implode("\n", $output), Debugger::E_ERROR);
        }

        return $output;
    }

    /**
     * Create a bootstrap for trial runs
     *
     * @internal
     */
    private function createBootstrap(string $file): string {
        $bootstrap = "declare(strict_types=1);";
        $bootstrap .= "class __SPL_Callbacks {";
        $bootstrap .= "private static \$map = [";

        foreach ($this->classmap as $class => $path) {
            $bootstrap .= "\"".str_replace("\\", "\\\\", $class)."\" => \"{$path}\",";
        }

        $bootstrap .= "];";
        $bootstrap .= "public static function class_autoload(\$class){";
        $bootstrap .= "if (isset(static::\$map[\$class])){";
        $bootstrap .= "require static::\$map[\$class];";
        $bootstrap .= "}}";
        $bootstrap .= "public static function error_handler(\$severity,\$message,\$file,\$line){";
        $bootstrap .= "throw new ErrorException(\$message,0,\$severity,\$file,\$line);";
        $bootstrap .= "}}";
        $bootstrap .= "spl_autoload_register([\"__SPL_Callbacks\", \"class_autoload\"]);";
        $bootstrap .= "set_error_handler([\"__SPL_Callbacks\", \"error_handler\"]);";
        $bootstrap .= "require \"$file\";";

        return $bootstrap;
    }
}
