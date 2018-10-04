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
use jacknoordhuis\classinformationdumper\model\InterfaceModel;
use jacknoordhuis\classinformationdumper\model\TraitModel;
use jacknoordhuis\classinformationdumper\visitor\ClassVisitor;
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

    /**
     * The class visitor that scrapes information from the traverser.
     *
     * @var ClassVisitor
     */
    private $visitor;

    public function __construct(string $namespace)
    {
        $this->subject_directory = $namespace;

        $this->visitor = new ClassVisitor();

        $this->fetchModels();
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
     * Fetch all the data from the subject directories files.
     */
    protected function fetchModels(): void
    {
        foreach ($this->getFiles() as $file) {
            try {
                $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                $traverser = new NodeTraverser;

                $stmts = $parser->parse(file_get_contents($file));
                $traverser->addVisitor($this->visitor);

                $traverser->traverse($stmts);
            } catch (\Throwable $e) {
                echo 'Unable to retrieve information for file "'.$file.'". '.$e->getMessage().'. Line: '.$e->getLine().' File: '.$e->getFile();
            }
        }
    }

    /**
     * Get an array of all the class models with their fully qualified namespace as the key.
     *
     * @return \jacknoordhuis\classinformationdumper\model\ClassModel[]
     */
    public function getClassModels(): array
    {
        $classes = $this->visitor->getClasses();

        return array_combine(
            array_map(function(ClassModel $model) {
                return $model->getFullyQualifiedNamespace();
            }, $classes), $classes);
    }

    /**
     * Get an array of all the interface models with their fully qualified namespace as the key.
     *
     * @return \jacknoordhuis\classinformationdumper\model\InterfaceModel[]
     */
    public function getInterfaceModels(): array
    {
        $interfaces = $this->visitor->getInterfaces();

        return array_combine(
            array_map(function(InterfaceModel $model) {
                return $model->getFullyQualifiedNamespace();
            }, $interfaces), $interfaces);
    }

    /**
     * Get an array of all the trait models with their fully qualified namespace as the key.
     *
     * @return array
     */
    public function getTraitModels(): array
    {
        $traits = $this->visitor->getTraits();

        return array_combine(
            array_map(function(TraitModel $model) {
                return $model->getFullyQualifiedNamespace();
            }, $traits), $traits);
    }

    /**
     * Get an array of all the classes and their constants, properties and methods.
     *
     * @return array
     */
    public function getClassInformation(): array
    {
        $classes = $this->getClassModels();

        return array_map(function(ClassModel $model) {
                return $model->getInformation();
            }, $classes);
    }

    /**
     * Get an array of all the interfaces and their constants and methods.
     *
     * @return array
     */
    public function getInterfaceInformation(): array
    {
        $interfaces = $this->getInterfaceModels();

        return array_map(function(InterfaceModel $model) {
                return $model->getInformation();
            }, $interfaces);
    }

    /**
     * Get an array of all the traits and their properties and methods.
     *
     * @return array
     */
    public function getTraitInformation(): array
    {
        $traits = $this->getTraitModels();

        return array_map(function(TraitModel $model) {
            return $model->getInformation();
        }, $traits);
    }

    /**
     * Get an array of all the class and interface models and their constants, properties and methods.
     *
     * @return array
     */
    public function getModelInformation(): array
    {
        return [
            'classes' => $this->getClassInformation(),
            'interfaces' => $this->getInterfaceInformation(),
            'traits' => $this->getTraitInformation(),
        ];
    }

    /**
     * Get an array of all php files in the subject directory.
     *
     * @return array
     */
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