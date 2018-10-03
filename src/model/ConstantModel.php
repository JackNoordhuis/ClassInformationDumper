<?php

/**
 * ConstantModel.php – ClassInformationDumper
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

class ConstantModel extends ModelVisiblity
{
    /** @var string */
    protected $name = '';

    /** @var mixed */
    protected $value;

    /**
     * @param string $name
     * @param int $flags
     * @param mixed $value
     */
    public function __construct(string $name, int $flags, $value)
    {
        $this->name = $name;
        $this->flags = $flags;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getInformation(): array
    {
        return array_merge([
            'name' => $this->name,
            'value' => $this->value,
        ], $this->getAdditionalInformation());
    }
}