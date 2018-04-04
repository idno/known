<?php

    namespace Tests {

        class EnvironmentTest extends KnownTestCase {

            /** 
             * Assert a compatible version of PHP
             */
            function testPHPVersion() {
                $this->assertTrue(version_compare(phpversion(), '5.4', '>='));
            }
            
            /**
             * Assert that required extension modules are present
             */
            function testExtensions() {
                echo "Checking extensions\n";
                foreach (
                         //    'curl','date','dom','gd','json','libxml','mbstring','mysql','reflection','session','simplexml', 'openssl'
                         //'curl','date','dom','gd','json','libxml','mbstring','pdo','pdo_mysql','reflection','session','simplexml', 'openssl'
                         \Idno\Core\Installer::requiredModules()
                          as $extension) {
                    echo "$extension\n";
                    $this->assertTrue(extension_loaded($extension));
                }
                
                echo "Checking available DB (mysql, mongodb, sqlite, pgsql)\n";
                $this->assertTrue(extension_loaded('mysql') || extension_loaded('mongodb') || extension_loaded('sqlite') || extension_loaded('pgsql'));
            }
            
            /** 
             * Assert that configuration files have been installed correctly
             */
            function testKnownConfigFileExists() {
                $this->assertTrue(file_exists(dirname(dirname(__FILE__)). '/configuration/config.ini'));
            }
            
            /** 
             * Assert that htaccess is there
             */
            function testHTAccessExists() {
                $this->assertTrue(file_exists(dirname(dirname(__FILE__)). '/.htaccess'));
            }
            
            /**
             * Assert that the configuration has been loaded correctly
             */
            function testKnownConfig() {
                $this->assertFalse(\Idno\Core\Idno::site()->config()->isDefaultConfig());
            }
        }

    }