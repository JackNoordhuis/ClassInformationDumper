<?php

/**
 * ClassInformation.php – ClassInformationDumper
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

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Fetches an individual classes information.
 */
class ClassInformation
{
    /**
     * Fully qualified class name of the subject.
     *
     * @var string
     */
    private $subject_name;

    /**
     * The reflection object used to retrieve information about the subject.
     *
     * @var ReflectionClass
     */
    private $subject_reflection;

    public function __construct(string $name)
    {
        $this->subject_name = $name;

        $this->subject_reflection = new ReflectionClass($name);
    }

    /**
     * Get the fully qualified namespace of the subject class
     *
     * @return string
     */
    public function getSubjectName(): string
    {
        return $this->subject_name;
    }

    /**
     * Get the reflection object used to retrieve information about the subject.
     *
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass
    {
        return $this->subject_reflection;
    }

    /**
     * Get an array of all the constants, properties and methods the subject has.
     *
     * @return array
     */
    public function getInformation(): array
    {
        return [
            'constants' => $this->getConstantInformation(),
            'properties' => $this->getPropertyInformation(),
            'methods' => $this->getMethodInformation(),
        ];
    }

    /**
     * Get an array of all the protected or public class constants.
     *
     * @return array
     */
    public function getConstantInformation(): array
    {
        $constants = [];
        foreach ($this->subject_reflection->getReflectionConstants() as $const) {
            if (! $const->isPrivate()) {
                $constants[] = $const->getName();
            }
        }

        return $constants;
    }

    /**
     * Get an array of all protected or public, static and non-static properties.
     *
     * @return array
     */
    public function getPropertyInformation(): array
    {
        $properties = [
            'normal' => [],
            'static' => [],
        ];

        $filter = ReflectionProperty::IS_STATIC | ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;

        foreach ($this->subject_reflection->getProperties($filter) as $property) {
            $properties[$property->isStatic() ? 'static' : 'normal'][] = $property->getName();
        }

        return $properties;
    }

    /**
     * Get an array of all protected or public, static and non-static methods.
     *
     * @return array
     */
    public function getMethodInformation(): array
    {
        $methods = [
            'normal' => [],
            'static' => [],
        ];

        $filter = ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED;

        foreach ($this->subject_reflection->getMethods($filter) as $method) {
            $methods[$method->isStatic() ? 'static' : 'normal'][] = $method->getName();
        }

        return $methods;
    }
}