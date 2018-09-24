<?php

namespace Tests\Core {

    class DummyPage extends \Idno\Common\Page
    {
    }

    class InputTest extends \Tests\KnownTestCase
    {

        public function testInputDefaults()
        {

            $this->assertTrue(null === \Idno\Core\Input::getInput('nulltest', null));
            $this->assertTrue(false === \Idno\Core\Input::getInput('falsetest', false));
            $this->assertTrue(true === \Idno\Core\Input::getInput('truetest', true));

            $page = new DummyPage();

            $this->assertTrue(null === $page->getInput('nulltest', null));
            $this->assertTrue(false === $page->getInput('falsetest', false));
            $this->assertTrue(true === $page->getInput('truetest', true));
        }

    }

}
