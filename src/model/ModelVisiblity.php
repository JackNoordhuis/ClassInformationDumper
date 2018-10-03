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

abstract class ModelVisiblity extends Model
{

    public const FLAG_PUBLIC = 0;
    public const FLAG_PROTECTED = 1;
    public const FLAG_PRIVATE = 2;

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->checkFlag(self::FLAG_PUBLIC);
    }

    /**
     * @return bool
     */
    public function isProtected(): bool
    {
        return $this->checkFlag(self::FLAG_PROTECTED);
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->checkFlag(self::FLAG_PRIVATE);
    }

    /**
     * @return array
     */
    public function getAdditionalInformation() : array {
        return [
            'public' => $this->isPublic(),
            'protected' => $this->isProtected(),
            'private' => $this->isPrivate(),
        ];
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
        $flags = 0;

        if ($public) {
            $flags ^= 1 << self::FLAG_PUBLIC;
        }

        if ($protected) {
            $flags ^= 1 << self::FLAG_PROTECTED;
        }

        if ($private) {
            $flags ^= 1 << self::FLAG_PRIVATE;
        }

        return $flags;
    }
}