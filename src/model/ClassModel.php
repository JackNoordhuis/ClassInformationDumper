<?php

/**
 * ClassModel.php – ClassInformationDumper
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

namespace jacknoordhuis\classinformationdumper\model;

class ClassModel extends Model
{
    /** @var string */
    protected $namespace = '';

    /** @var string */
    protected $shortName = '';

    /** @var \jacknoordhuis\classinformationdumper\Model\ConstantModel[] */
    protected $constants = [];

    /** @var \jacknoordhuis\classinformationdumper\Model\PropertyModel[] */
    protected $properties = [];

    /** @var \jacknoordhuis\classinformationdumper\Model\MethodModel[] */
    protected $methods = [];

    public const FLAG_ABSTRACT = 4;
    public const FLAG_FINAL = 5;

    public const FLAG_ANONYMOUS = 7;

    public function __construct(string $namespace, string $shortName, int $flags, array $constants = [], array $properties = [], array $methods = [])
    {
        $this->namespace = $namespace;
        $this->shortName = $shortName;
        $this->flags = $flags;
        $this->constants = $constants;
        $this->properties = $properties;
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedNamespace(): string
    {
        return $this->namespace.'\\'.$this->shortName;
    }

    /**
     * @return \jacknoordhuis\classinformationdumper\Model\ConstantModel[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @param \jacknoordhuis\classinformationdumper\model\ConstantModel $constant
     */
    public function addConstant(?ConstantModel $constant): void
    {
        if ($constant === null) {
            return;
        }

        $this->constants[] = $constant;
    }

    /**
     * @return \jacknoordhuis\classinformationdumper\Model\PropertyModel[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param \jacknoordhuis\classinformationdumper\model\PropertyModel $property
     */
    public function addProperty(?PropertyModel $property): void
    {
        if ($property === null) {
            return;
        }

        $this->properties[] = $property;
    }

    /**
     * @return \jacknoordhuis\classinformationdumper\Model\MethodModel[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param \jacknoordhuis\classinformationdumper\model\MethodModel $method
     */
    public function addMethod(?MethodModel $method): void
    {
        if ($method === null) {
            return;
        }

        $this->methods[] = $method;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->checkFlag(self::FLAG_ABSTRACT);
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->checkFlag(self::FLAG_FINAL);
    }

    /**
     * @return bool
     */
    public function isAnonymous(): bool
    {
        return $this->checkFlag(self::FLAG_ANONYMOUS);
    }

    /**
     * @return array
     */
    public function getInformation(): array
    {
        return [
            'namespace' => $this->namespace,
            'name' => $this->shortName,
            'fully_namespace' => $this->getFullyQualifiedNamespace(),
            'constants' => array_map(function (ConstantModel $constant) {
                return $constant->getInformation();
            }, $this->constants),
            'properties' => array_map(function (PropertyModel $property) {
                return $property->getInformation();
            }, $this->properties),
            'methods' => array_map(function (MethodModel $method) {
                return $method->getInformation();
            }, $this->methods),
            'abstract' => $this->isAbstract(),
            'final' => $this->isFinal(),
            'anonymous' => $this->isAnonymous(),
        ];
    }

    /**
     * @param bool $abstract
     * @param bool $final
     * @param bool $anonymous
     *
     * @return int
     */
    public static function buildFlags(bool $abstract = false, bool $final = false, bool $anonymous = false): int
    {
        $flags = 0;

        if ($abstract) {
            $flags ^= 1 << self::FLAG_ABSTRACT;
        }

        if ($final) {
            $flags ^= 1 << self::FLAG_FINAL;
        }

        if ($anonymous) {
            $flags ^= 1 << self::FLAG_ANONYMOUS;
        }

        return $flags;
    }
}