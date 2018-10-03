<?php

/**
 * class-info.php – ClassInformationDumper
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

require 'vendor/autoload.php';

$OUT = null;

$pos = array_search('-o', $argv);
if (! isset($pos) and $pos !== false) {
    $OUT = $argv[$pos + 1];
}

$o = new \jacknoordhuis\classinformationdumper\DirectoryInformation(__DIR__.'/src');

var_dump($o->getClassInformation());