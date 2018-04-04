<?php

spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $basedir = dirname(dirname(__FILE__)) . '/';
    if (file_exists($basedir . $class . '.php')) {
        include_once($basedir . $class . '.php');
    }
});

require_once(dirname(__FILE__) . '/WebInstaller.php');

$installer = WebInstaller::installer();
$installer->run();
