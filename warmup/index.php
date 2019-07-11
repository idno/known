<?php

require_once(dirname(dirname(__FILE__)) . '/vendor/autoload.php');
    
require_once(dirname(__FILE__) . '/WebInstaller.php');

$installer = WebInstaller::installer();
$installer->run();
