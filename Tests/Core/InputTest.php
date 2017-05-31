<?php

namespace Tests\Core {

    class InputTest extends \Tests\KnownTestCase {

        public function testInputDefaults() {
            
            $this->assertTrue(null === \Idno\Core\Input::getInput('nulltest', null));
            $this->assertTrue(false === \Idno\Core\Input::getInput('falsetest', false));
            $this->assertTrue(true === \Idno\Core\Input::getInput('truetest', true));
        }

    }

}
