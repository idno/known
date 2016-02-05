<?php

define('KNOWN_UNIT_TEST', true);

// Set some environment: Use export KNOWN_DOMAIN / KNOWN_PORT to override from the command line
$domain = getenv('KNOWN_DOMAIN');
if (!$domain && isset($_SERVER['SERVER_NAME']))
    $domain = $_SERVER['SERVER_NAME'];
if (!$domain)
    $domain = 'localhost';
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
    
    // Register test classes with class loader
    loader()->registerNamespace('Tests', dirname(dirname(__FILE__)));

    // TODO: Initialise and populate test DB


} catch (Exception $ex) {
    echo $ex->getMessage();
}