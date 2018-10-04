<?php

/**
 * InterfaceModel.php – ClassInformationDumper
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

class InterfaceModel extends Model
{
    /** @var string */
    protected $namespace = '';

    /** @var string */
    protected $shortName = '';

    /** @var \jacknoordhuis\classinformationdumper\Model\ConstantModel[] */
    protected $constants = [];

    /** @var \jacknoordhuis\classinformationdumper\Model\MethodModel[] */
    protected $methods = [];

    public function __construct(string $namespace, string $shortName, array $constants = [], array $methods = [])
    {
        $this->namespace = $namespace;
        $this->shortName = $shortName;
        $this->constants = $constants;
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
            'methods' => array_map(function (MethodModel $method) {
                return $method->getInformation();
            }, $this->methods),
        ];
    }
}