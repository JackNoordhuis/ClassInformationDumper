<?php

/**
 * directory-info.php – ClassInformationDumper
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

$ARGS = $argv;

$FORMAT = 'json';
$OUT = null;

$DIRECTORY = null;

array_shift($ARGS); // remove the script name from the arguments list

$pos = array_search('-f', $ARGS);
if (! isset($pos) or $pos !== false) {
    $FORMAT = strtolower($ARGS[$pos + 1]);

    switch ($FORMAT) {
        case 'json':
        case 'php':
        case 'serialize':
            fprintf(STDERR, 'Set output format to \'%s\'%s', $FORMAT, PHP_EOL);
            break;
        default:
            fprintf(STDERR, 'Unsupported format specified \'%s\'%s', $ARGS[$pos + 1], PHP_EOL);
            exit;
    }

    array_splice($ARGS, $pos, 2); // remove argument from array
}

$pos = array_search('-o', $ARGS);
if (! isset($pos) or $pos !== false) {
    $OUT = __DIR__.DIRECTORY_SEPARATOR.$ARGS[$pos + 1];

    array_splice($ARGS, $pos, 2); // remove argument from array
}

switch (count($ARGS)) {
    case 1:
        $DIRECTORY = realpath($ARGS[0]);
        if ($DIRECTORY === false or ! is_dir($DIRECTORY)) {
            fprintf(STDERR, 'Error:\\tSpecified directory \'%s\' could not be found or is not a directory!%s', ($DIRECTORY !== false) ? $DIRECTORY : $ARGS[0], PHP_EOL);
            exit;
        }
        fprintf(STDERR, 'Set scan directory to \'%s\'%s', ($DIRECTORY !== false) ? $DIRECTORY : $ARGS[0], PHP_EOL);
        break;
    default:
        fprintf(STDERR, 'Error:\\tToo much parameters are specified!%s', PHP_EOL);
        exit;
}

$info = (new \jacknoordhuis\classinformationdumper\DirectoryInformation($DIRECTORY))->getClassInformation();

if ($OUT === null) {
    fprintf(STDERR, '%s%s', json_encode($info), PHP_EOL);
}
$file = fopen($OUT, 'w') or die("Unable to open output file!\n");
switch ($FORMAT) {
    case 'json':
        fwrite($file, json_encode($info));
        break;
    case 'php':
        fwrite($file, '<?php'.PHP_EOL.PHP_EOL);
        fwrite($file, var_export($info, true).';');
        break;
    case 'serialize':
        fwrite($file, serialize($info));
        break;
}

fclose($file);