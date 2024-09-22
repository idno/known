<?php

// Intentionally loading vendor libraries before bootstrapping from environment variables,
// so we can use .env files. require_once ensures that our call to start.php later on won't
// cause an issue, even though we're calling this twice.
if (file_exists(dirname(dirname(__FILE__)) . '/vendor/autoload.php')) {
    include_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
}

if (file_exists(dirname(dirname(__FILE__)) . '/.env')) {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(dirname(__FILE__))); // @TODO remove unsafe once we've moved from getenv across the board
    $dotenv->load();
}

define('KNOWN_UNIT_TEST', true);

// Set some environment: Use export KNOWN_DOMAIN / KNOWN_PORT to override from the command line
$domain = 'localhost';
if (\Idno\Core\Idno::site()->request()->server->has('KNOWN_DOMAIN')) {
    $domain = \Idno\Core\Idno::site()->request()->server->get('KNOWN_DOMAIN');
}

if (!$domain && \Idno\Core\Idno::site()->request()->server->has('SERVER_NAME')) {
    $domain = \Idno\Core\Idno::site()->request()->server->get('SERVER_NAME');
}

\Idno\Core\Idno::site()->request()->server->set('SERVER_NAME', $domain);

$port = getenv('KNOWN_PORT');
if (!$port && \Idno\Core\Idno::site()->request()->server->has('SERVER_PORT')) {
    $port =\Idno\Core\Idno::site()->request()->server->get('SERVER_PORT');
}
if (!$port) {
    $port = 80;
}
\Idno\Core\Idno::site()->request()->server->set('SERVER_PORT', $port);

try {
    // Load Known framework
    include_once dirname(dirname(__FILE__)) . '/Idno/start.php';

} catch (Exception $ex) {
    echo $ex->getMessage();
}
