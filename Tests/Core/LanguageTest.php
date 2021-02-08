<?php

namespace Tests\Core {

    class EnglishTest extends \Idno\Core\ArrayKeyTranslation
    {

        public function getStrings()
        {
            return [
                'Hello!' => 'Hello!'
            ];
        }

    }

    class FrenchTest extends \Idno\Core\ArrayKeyTranslation
    {

        public function getStrings()
        {
            return [
                'Hello!' => 'Bonjour!'
            ];
        }

    }

    class LanguageTest extends \Tests\KnownTestCase
    {

        public function testLanguageString()
        {

            $english = new \Idno\Core\Language('en_GB');
            $french = new \Idno\Core\Language('fr_FR');

            $english->register(new EnglishTest('en_GB'));
            $english->register(new FrenchTest('fr_FR'));

            $french->register(new EnglishTest('en_GB'));
            $french->register(new FrenchTest('fr_FR'));

            echo "English: " . $english->_('Hello!');
            echo "\nFrench: " . $french->_('Hello!');

            $txt = $english->_('Hello!');
            $this->assertNotEmpty($txt, 'A translation for "Hello!" should have been found in the English language.');
            $txt2 = $french->_('Hello!');
            $this->assertNotEmpty($txt2, 'A translation for "Hello!" should have been found in the French language.');
            $this->assertFalse($french->_('Hello!') == $english->_('Hello!'), 'The English translation should not have been the same as the French translation of "Hello!".');
        }

    }

}
