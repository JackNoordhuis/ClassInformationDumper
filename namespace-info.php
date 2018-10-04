<?php

/**
 * namespace-info.php – ClassInformationDumper
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

$NAMESPACE = null;

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
        $NAMESPACE = $ARGS[0];
        fprintf(STDERR, 'Set scan namespace to \'%s\'%s', $NAMESPACE, PHP_EOL);
        break;
    default:
        fprintf(STDERR, 'Error:\\tToo much parameters are specified!%s', PHP_EOL);
        exit;
}

$info = (new \jacknoordhuis\classinformationdumper\NamespaceInformation($NAMESPACE))->getClassInformation();

$lines = [];
switch ($FORMAT) {
    case 'json':
        $lines[] = json_encode($info);
        break;
    case 'php':
        $lines[] = '<?php'.PHP_EOL.PHP_EOL;
        $lines[] = var_export($info, true).';';
        break;
    case 'serialize':
        $lines[] = serialize($info);
        break;
}

if ($OUT === null) {
    foreach($lines as $line) {
        fprintf(STDERR, '%s', $line);
    }
} else {
    $file = fopen($OUT, 'w') or die("Unable to open output file!\n");
    foreach($lines as $line) {
        fwrite($file, $line);
    }
    fclose($file);
}