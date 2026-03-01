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

    class LanguageTest extends \Tests\IdnoTestCase
    {

        public function testLanguageString()
        {

            $english = new \Idno\Core\Language('en_GB');
            $french = new \Idno\Core\Language('fr_FR');

            $english->register(new EnglishTest('en_GB'));
            $english->register(new FrenchTest('fr_FR'));

            $french->register(new EnglishTest('en_GB'));
            $french->register(new FrenchTest('fr_FR'));

            $txt = $english->_('Hello!');
            $this->assertNotEmpty($txt, 'A translation for "Hello!" should have been found in the English language.');
            $txt2 = $french->_('Hello!');
            $this->assertNotEmpty($txt2, 'A translation for "Hello!" should have been found in the French language.');
            $this->assertNotEquals($french->_('Hello!'), $english->_('Hello!'), 'The English translation should not have been the same as the French translation of "Hello!".');
        }

        public function testTranslationFallback()
        {
            $lang = new \Idno\Core\Language('en_US');

            // With no translations registered, should return the original string
            $result = $lang->_('Untranslated string');
            $this->assertEquals('Untranslated string', $result);
        }

        public function testTranslationFallbackReturnsFalse()
        {
            $lang = new \Idno\Core\Language('en_US');

            // With failover=false, should return false for missing translation
            $result = $lang->getTranslation('Missing string', false);
            $this->assertFalse($result);
        }

        public function testSubstitution()
        {
            $lang = new \Idno\Core\Language('en_US');

            $result = $lang->_('Hello %s, you have %d messages', ['World', 5]);
            $this->assertEquals('Hello World, you have 5 messages', $result);
        }

        public function testHtmlEscaping()
        {
            $lang = new \Idno\Core\Language('en_US');

            $result = $lang->esc_('Hello <b>%s</b>', ['<script>alert("xss")</script>']);
            $this->assertStringNotContainsString('<script>', $result);
            $this->assertStringContainsString('&lt;script&gt;', $result);
        }

        public function testGetLanguage()
        {
            $lang = new \Idno\Core\Language('fr_FR');
            $this->assertEquals('fr_FR', $lang->getLanguage());
        }

        public function testRegisterOnlyAcceptsMatchingLanguage()
        {
            $english = new \Idno\Core\Language('en_GB');

            // Register French translation for English language - should be ignored
            $english->register(new FrenchTest('fr_FR'));

            // Should return fallback since French translation was rejected
            $result = $english->_('Hello!');
            $this->assertEquals('Hello!', $result);
        }

        public function testMagicGet()
        {
            $lang = new \Idno\Core\Language('en_GB');
            $lang->register(new EnglishTest('en_GB'));

            // __get should work like get()
            $result = $lang->{'Hello!'};
            $this->assertEquals('Hello!', $result);
        }

        public function testUncurlQuotes()
        {
            $lang = new \Idno\Core\Language('en_US');

            // Test that curly quotes are converted to straight quotes
            $result = $lang->uncurlQuotes("\xE2\x80\x9CHello\xE2\x80\x9D");
            $this->assertEquals('"Hello"', $result);

            $result2 = $lang->uncurlQuotes("\xE2\x80\x98it\xE2\x80\x99s\xE2\x80\x99");
            $this->assertStringContainsString("'", $result2);
        }

        public function testDetectBrowserLanguage()
        {
            // Test with no browser language set
            unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $lang = \Idno\Core\Language::detectBrowserLanguage();
            $this->assertIsString($lang);
        }

        public function testDetectBrowserLanguageWithHeader()
        {
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr-FR,fr;q=0.9,en-US;q=0.8';
            $lang = \Idno\Core\Language::detectBrowserLanguage();
            $this->assertEquals('fr_FR', $lang);

            // Restore
            unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        public function testDetectBrowserLanguageShort()
        {
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9';
            $lang = \Idno\Core\Language::detectBrowserLanguage(false);
            $this->assertEquals('de', $lang);

            // Restore
            unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

    }

}
