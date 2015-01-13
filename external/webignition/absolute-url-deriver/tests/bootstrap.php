<?php
ini_set('display_errors', 'On');
ini_set('error_reporting', -1);

require_once  __DIR__ . '/../vendor/autoload.php';

function autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {        
        $file = sprintf(
            '%s/%s.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );        
        
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

autoload( '/usr/share/php' );
autoload( __DIR__ . '/');