<?php

/**
 * Helper.php – ClassInformationDumper
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

namespace jacknoordhuis\classinformationdumper\utils;

abstract class Helper
{
    /**
     * Strip numeric keys from a PHP array generate from var_export.
     *
     * @param string $input
     *
     * @return string
     */
    public static function stripNumericKeys(string $input): string
    {
        return preg_replace("/[0-9]+ \=\>/i", '', $input);
    }

    /**
     * Strip blank lines from an input string.
     *
     * @param string $input
     *
     * @return string
     */
    public static function stripBlankLines(string $input): string
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $input);
    }
}