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

use jacknoordhuis\classinformationdumper\model\ClassModel;
use jacknoordhuis\classinformationdumper\visitor\ClassVisitor;
use jacknoordhuis\classinformationdumper\visitor\NamespaceVisitor;
use PhpParser\NodeTraverser;
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
    public function getSubjectNamespace(): string
    {
        return $this->subject_directory;
    }

    /**
     * @return \jacknoordhuis\classinformationdumper\model\ClassModel[]
     */
    public function getClassModels(): array
    {
        $classes = [];

        foreach ($this->getFiles() as $file) {
            try {
                $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                $traverser = new NodeTraverser;

                $stmts = $parser->parse(file_get_contents($file));
                $traverser->addVisitor($classVisitor = new ClassVisitor());

                $traverser->traverse($stmts);

                foreach ($classVisitor->getClasses() as $class) {
                    $classes[$class->getFullyQualifiedNamespace()] = $class;
                }
            } catch (\Throwable $e) {
                echo 'Unable to retrieve information for file "'.$file.'". '.$e->getMessage().'. Line: '.$e->getLine().' File: '.$e->getFile();
            }
        }

        return $classes;
    }

    /**
     * Get an array of all the classes and their constants, properties and methods.
     *
     * @return array
     */
    public function getClassInformation(): array
    {
        $classes = $this->getClassModels();

        return array_combine(
            array_map(function(ClassModel $model) {
                return $model->getFullyQualifiedNamespace();
            }, $classes),
            array_map(function(ClassModel $model) {
                return $model->getInformation();
            }, $classes)
        );
    }

    public function getFiles()
    {
        $files = [];

        /** @var \SplFileInfo $file */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->subject_directory)) as $file) {
            if ($file->getExtension() === "php") {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }
}