<?php

/**
 * DirectoryInformation.php – ClassInformationDumper
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

namespace jacknoordhuis\classinformationdumper;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Scans a directory recursively for PHP files, scans/parses the individual php files for class
 * definitions (allowing multiple classes in each file, breaking most auto-loading standards), then
 * fetches the individual class information.
 */
class DirectoryInformation
{
    /**
     * The subject namespace.
     *
     * @var string
     */
    private $subject_directory;

    public function __construct(string $namespace)
    {
        $this->subject_directory = $namespace;
    }

    /**
     * Get the fully qualified namespace.
     *
     * @return string
     */
    public function getSubjectNamespace() : string {
        return $this->subject_directory;
    }

    /**
     * Get an array of all the classes and their constants, properties and methods.
     *
     * @return array
     */
    public function getClassInformation() : array {
        $classes = [];

        foreach($this->getFiles() as $file) {
            try {
                $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                $traverser     = new NodeTraverser;

                $stmts = $parser->parse(file_get_contents($file));

                $traverser->addVisitor($visitor = new class extends NodeVisitorAbstract {
                    public $namespace = '';
                    public $classes = [];

                    public function leaveNode(Node $node)
                    {
                        if($node instanceof Node\Stmt\Namespace_) {
                            $this->namespace = $node->name->toString();
                        }
                        if($node instanceof Node\Stmt\Class_) {
                            if($node->name instanceof Node\Identifier) {
                                $this->classes[] = $node->name->name;
                            }
                        }
                    }
                });

                $traverser->traverse($stmts);

                require_once $file;

                foreach($visitor->classes as $class) {
                    $classes[$visitor->namespace . '\\' . $class] = (new ClassInformation($visitor->namespace . '\\' . $class))->getInformation();
                }
            } catch(\Throwable $e) {
                echo 'Unable to retrieve information for file "' . $file . '". ' . $e->getMessage() . '. Line: ' . $e->getLine() . ' File: ' . $e->getFile();
            }
        }

        return $classes;
    }

    public function getFiles() {
        $files = [];

        /** @var \SplFileInfo $file */
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->subject_directory)) as $file) {
            if($file->getExtension() === "php") {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }
}