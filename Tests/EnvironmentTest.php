<?php

    namespace Tests {

        class EnvironmentTest extends \PHPUnit_Framework_TestCase {

            /** 
             * Assert a compatible version of PHP
             */
            function testPHPVersion() {
                $this->assertTrue(version_compare(phpversion(), '5.4', '>='));
            }
            
            /** 
             * Assert that configuration files have been installed correctly
             */
            function testKnownConfigFileExists() {
                $this->assertTrue(file_exists(dirname(dirname(__FILE__)). '/config.ini'));
            }
            
            
            /** 
             * Assert that htaccess is there
             */
            function testHTAccessExists() {
                $this->assertTrue(file_exists(dirname(dirname(__FILE__)). '/.htaccess'));
            }
        }

    }