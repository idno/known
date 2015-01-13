<?php

/**
 * Check that Query::contains() works as expected
 *  
 */
class QueryContainsTest extends AbstractRegularUrlTest {      
    
    public function testQueryContains() {        
        $url = new \webignition\Url\Url('http://example.com/?key1=value&key2=value2');
        $this->assertTrue($url->getQuery()->contains('key1'));        
        $this->assertFalse($url->getQuery()->contains('key3'));
    }   
}