<?php

/**
 * PropertyModel.php – ClassInformationDumper
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

class PropertyModel extends ModelStatic
{
    /** @var string */
    protected $name = '';

    /** @var mixed */
    protected $defaultValue;

    /**
     * @param string $name
     * @param int $flags
     * @param mixed $defaultValue
     */
    public function __construct(string $name, int $flags, $defaultValue)
    {
        $this->name = $name;
        $this->flags = $flags;
        $this->defaultValue = $defaultValue;
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
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    public function getInformation(): array
    {
        return array_merge([
            'name' => $this->name,
            'default_value' => $this->defaultValue,
        ], $this->getAdditionalInformation());
    }
}