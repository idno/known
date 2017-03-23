<?php


if (!class_exists('PHPUnit_Framework_TestCase')) {
    
    /**
     * Travis CI has moved to PHPUnit 5.6, but we still need comptibility with older versions for local testing.
     * See the conversation here (https://github.com/travis-ci/travis-ci/issues/7226) for details.
     */
    class PHPUnit_Framework_TestCase extends \PHPUnit\Framework\TestCase {}
    
}