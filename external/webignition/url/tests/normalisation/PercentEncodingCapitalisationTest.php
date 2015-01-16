<?php

/**
 * Check that normalisation capitalises percent-encoded entities
 *  
 */
class PercentEncodingCapitalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlCapitalisesPercentEncodedEntities() {      
        $reservedCharacters = array('!','*',"'",'(',')',';',':','@','&','=','+','$',',','/','?','#','[',']');
            
        $encodedKeyValuePairs = array();
        $lowercaseEncodedKeyValuePairs = array();
        
        $keyIndex = 0;
        
        foreach  ($reservedCharacters as $reservedCharacter) {
            $key = 'key'.$keyIndex;
            
            $encodedKeyValuePairs[$key] = urlencode($reservedCharacter);
            $lowercaseEncodedKeyValuePairs[$key] = strtolower(urlencode($reservedCharacter));
            
            $keyIndex++;
        }
        
        ksort($encodedKeyValuePairs);        
        ksort($lowercaseEncodedKeyValuePairs);
        
        $percentEncodedQueryString = '';
        $lowercasePercentEncodedQueryString = '';
        
        foreach ($encodedKeyValuePairs as $key => $value) {
            $percentEncodedQueryString .= '&' . urlencode($key).'='.$value;
        }
        
        foreach ($lowercaseEncodedKeyValuePairs as $key => $value) {
            $lowercasePercentEncodedQueryString .= '&' . urlencode($key).'='.$value;
        }
        
        $percentEncodedQueryString = substr($percentEncodedQueryString, 1);
        $lowercasePercentEncodedQueryString = substr($lowercasePercentEncodedQueryString, 1);
        
        $url = new \webignition\NormalisedUrl\NormalisedUrl(self::SCHEME_HTTP.'://'.self::HOST.'/?'.$lowercasePercentEncodedQueryString);
        $this->assertEquals($percentEncodedQueryString, (string)$url->getQuery());
    } 
}