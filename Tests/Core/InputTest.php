<?php

namespace Tests\Core {

    class DummyPage extends \Idno\Common\Page
    {
    }

    class InputTest extends \Tests\KnownTestCase
    {

        public function testInputDefaults()
        {

            $this->assertNull(\Idno\Core\Input::getInput('nulltest', null), 'getInput should return null when this is specified as the default value and no input with the specified name is found.');
            $this->assertTrue(false === \Idno\Core\Input::getInput('falsetest', false), 'getInput should return false when this is specified as the default value and no input with the specified name is found.');
            $this->assertTrue(true === \Idno\Core\Input::getInput('truetest', true), 'getInput should return true when this is specified as the default value and no input with the specified name is found.');

            $page = new DummyPage();

            $this->assertNull($page->getInput('nulltest', null), 'getInput should return null when this is specified as the default value and no input with the specified name is found.');
            $this->assertTrue(false === $page->getInput('falsetest', false), 'getInput should return false when this is specified as the default value and no input with the specified name is found.');
            $this->assertTrue(true === $page->getInput('truetest', true), 'getInput should return true when this is specified as the default value and no input with the specified name is found.');
        }

    }

}
