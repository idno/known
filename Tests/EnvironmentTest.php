<?php

namespace Tests {

    /**
     * @TODO This isn't a unit test and I'm not sure we need this file.
     */

    class EnvironmentTest extends KnownTestCase
    {

        /**
         * Assert a compatible version of PHP
         */
        function testPHPVersion()
        {
            $this->assertTrue(version_compare(phpversion(), '5.4', '>='), 'PHP should be at least version 5.4.');
        }

        /**
         * Assert that required extension modules are present
         */
        function testExtensions()
        {
            echo "Checking extensions\n";
            foreach (
                     //    'curl','date','dom','gd','json','libxml','mbstring','mysql','reflection','session','simplexml', 'openssl'
                     //'curl','date','dom','gd','json','libxml','mbstring','pdo','pdo_mysql','reflection','session','simplexml', 'openssl'
                     \Idno\Core\Installer::requiredModules()
                      as $extension) {
                echo "$extension " .var_export(extension_loaded($extension), true). "\n";
                $this->assertTrue(extension_loaded($extension));
            }

            echo "Checking available DB (mysql, mongodb, sqlite, pgsql)\n";
            $this->assertTrue(extension_loaded('pdo_mysql') || extension_loaded('mongodb') || extension_loaded('pdo_sqlite') || extension_loaded('pdo_pgsql'));
        }

        /**
         * Assert that the configuration has been loaded correctly
         */
        function testKnownConfig()
        {
            $this->assertFalse(\Idno\Core\Idno::site()->config()->isDefaultConfig());
        }
    }

}
