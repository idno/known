<?php

    namespace Tests {

        class EnvironmentTest extends \PHPUnit_Framework_TestCase {

            /** 
             * Assert a compatible version of PHP
             */
            function testPHPVersion() {
                $this->assertTrue(version_compare(phpversion(), '5.4', '>='));
            }
            
        }

    }