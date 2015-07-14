<?php

define('KNOWN_UNIT_TEST', true);

// Set some environment
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = 80;

// Load Known framework
require_once(dirname(dirname(__FILE__)) . '/Idno/start.php');

// TODO: Initialise and populate test DB
