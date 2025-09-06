<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Set\Php83SetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/Idno',
        __DIR__ . '/IdnoPlugins',
        __DIR__ . '/Themes',
        __DIR__ . '/ConsolePlugins',
        __DIR__ . '/Tests',
        __DIR__ . '/index.php',
        __DIR__ . '/known.php',
    ]);

    // Target PHP 8.3
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        Php83SetList::PHP_83,
    ]);
};

