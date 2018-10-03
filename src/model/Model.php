<?php

/**
 * Model.php – ClassInformationDumper
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

abstract class Model
{
    /** @var int */
    protected $flags = 0;

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @param int $flag
     *
     * @return bool
     */
    public function checkFlag(int $flag)
    {
        return ((int) $this->flags & (1 << $flag)) > 0;
    }

    /**
     * @param int $flag
     * @param bool $value
     */
    public function setFlag(int $flag, bool $value)
    {
        if ($this->checkFlag($flag) !== $value) {
            $this->flags ^= 1 << $flag;
        }
    }
}