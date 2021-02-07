<?php

// Intentionally loading vendor libraries before bootstrapping from environment variables,
// so we can use .env files. require_once ensures that our call to start.php later on won't
// cause an issue, even though we're calling this twice.
if (file_exists(dirname(dirname(__FILE__)) . '/vendor/autoload.php')) {
    require_once(dirname(dirname(__FILE__)) . '/vendor/autoload.php');
}

if (file_exists(dirname(dirname(__FILE__)) . '/.env')) {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(dirname(__FILE__))); // @TODO remove unsafe once we've moved from getenv across the board
    $dotenv->load();
}

define('KNOWN_UNIT_TEST', true);

// Set some environment: Use export KNOWN_DOMAIN / KNOWN_PORT to override from the command line
$domain = 'localhost';
if (isset($_SERVER['KNOWN_DOMAIN'])) $domain = $_SERVER['KNOWN_DOMAIN'];

if (!$domain && isset($_SERVER['SERVER_NAME']))
    $domain = $_SERVER['SERVER_NAME'];

$_SERVER['SERVER_NAME'] = $domain;

$port = getenv('KNOWN_PORT');
if (!$port && isset($_SERVER['SERVER_PORT']))
    $port = $_SERVER['SERVER_PORT'];
if (!$port)
    $port = 80;
$_SERVER['SERVER_PORT'] = $port;

try {
    // Load Known framework
    require_once(dirname(dirname(__FILE__)) . '/Idno/start.php');

} catch (Exception $ex) {
    echo $ex->getMessage();
}
