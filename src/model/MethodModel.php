<?php

/**
 * MethodModel.php – ClassInformationDumper
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

class MethodModel extends ModelFinal
{
    /** @var string */
    protected $name = '';

    public const FLAG_MAGIC = 6;

    /**
     * @return bool
     */
    public function isMagic(): bool
    {
        return $this->checkFlag(self::FLAG_MAGIC);
    }

    /**
     * @param string $name
     * @param int $flags
     */
    public function __construct(string $name, int $flags)
    {
        $this->name = $name;
        $this->flags = $flags;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getInformation(): array
    {
        return array_merge([
            'name' => $this->name,
            'magic' => $this->isMagic(),
        ], $this->getAdditionalInformation());
    }

    /**
     * @param bool $public
     * @param bool $protected
     * @param bool $private
     * @param bool $static
     * @param bool $abstract
     * @param bool $final
     * @param bool $magic
     *
     * @return int
     */
    public static function buildFlags(bool $public, bool $protected = false, bool $private = false, bool $static = false, bool $abstract = false, bool $final = false, bool $magic = false): int
    {
        $flags = parent::buildFlags($public, $protected, $private, $static, $abstract, $final, $magic);

        if ($static) {
            $flags ^= 1 << self::FLAG_MAGIC;
        }

        return $flags;
    }
}