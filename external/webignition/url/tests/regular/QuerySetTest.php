<?php

/**
 * Check that Query::set() works as it should
 *  
 */
class QuerySetTest extends AbstractRegularUrlTest {      
    
    public function testSet() {        
        $url = new \webignition\Url\Url('http://example.com/?key1=value&key2=value2');
        $url->getQuery()->set('key3', 'value3');
        
        $this->assertTrue($url->getQuery()->contains('key3'));
        $this->assertEquals('key1=value&key2=value2&key3=value3', (string)$url->getQuery());
        
        $url->getQuery()->set('key4', 'value4');
        $this->assertEquals('key1=value&key2=value2&key3=value3&key4=value4', (string)$url->getQuery());
    }  
}