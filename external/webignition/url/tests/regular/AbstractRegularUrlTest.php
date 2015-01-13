<?php

abstract class AbstractRegularUrlTest extends AbstractUrlTest {     
 
    /**
     *
     * @param string $inputUrl
     * @return \webignition\Url\Url 
     */
    protected function newUrl($inputUrl) {
        return new \webignition\Url\Url($inputUrl);
    }
}