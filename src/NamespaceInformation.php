<?php

/**
 * NamespaceInformation.php – ClassInformationDumper
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

use Corp104\Cache\ArrayCache;
use TheCodingMachine\ClassExplorer\Glob\GlobClassExplorer;

/**
 * Gathers information about a namespaces classes from the autoloader and then
 * fetches the individual class information.
 */
class NamespaceInformation
{
    /**
     * The subject namespace.
     *
     * @var string
     */
    private $subject_directory;

    /**
     * @var GlobClassExplorer
     */
    private $subject_explorer;

    public function __construct(string $namespace)
    {
        $this->subject_directory = $namespace;

        $this->subject_explorer = new GlobClassExplorer($namespace, new ArrayCache());
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
     * Get an array of all the classes and their constants, properties and methods.
     *
     * @return array
     */
    public function getClassInformation(): array
    {
        $classes = [];

        foreach ($this->subject_explorer->getClasses() as $file => $class) {
            try {
                $classes[$class] = (new ClassInformation($class))->getInformation();
            } catch (\Throwable $e) {
                echo 'Unable to retrieve information for class "'.$class.'". '.$e->getMessage().' File: '.$e->getFile().' at line: '.$e->getLine();
            }
        }

        return $classes;
    }
}