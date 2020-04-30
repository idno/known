<?php

namespace Tests\Core {

    use Idno\Core\DefaultTemplate;

    class TemplateTest extends \Tests\KnownTestCase
    {
        function parseURLsProvider() {
            return [
                'first' => [
                    "This links to a weird domain <a href=\"http://deals.blackfriday\" target=\"_blank\" >http://<wbr />deals.blackfriday</a>.",
                    "This links to a weird domain http://deals.blackfriday."
                ], 
                'second' => [
                    "<a href=\"http://starts.with.a.link\" target=\"_blank\" >http://<wbr />starts.with.a.link</a> and ends with <a href=\"https://kylewm.com/about#me\">HTML</a>.",
                    "http://starts.with.a.link and ends with <a href=\"https://kylewm.com/about#me\">HTML</a>."
                ], 
                'third' => [
                    "a matched parenthesis: <a href=\"http://wikipedia.org/Python_(programming_language)\" target=\"_blank\" >http://<wbr />wikipedia.org/<wbr />Python_(programming_language)</a> and (an unmatched parenthesis <a href=\"https://en.wikipedia.org/wiki/Guido_van_Rossum\" target=\"_blank\" >https://<wbr />en.wikipedia.org/<wbr />wiki/<wbr />Guido_van_Rossum</a>)",
                    "a matched parenthesis: http://wikipedia.org/Python_(programming_language) and (an unmatched parenthesis https://en.wikipedia.org/wiki/Guido_van_Rossum)"
                ],
            ];
        }

        /**
         * @param type $expected 
         * @param type $text
         * @dataProvider parseURLsProvider
         */
        function testParseURLs($expected, $text)
        {
            // adapted test cases from brevity (Known requires the http(s) prefix)
            $t = new DefaultTemplate();

            $this->assertEquals($expected, $t->parseURLs($text));
            

        }

        function testDataAttributes()
        {

            $t = new DefaultTemplate();
            $t->addDataToObjectType('testobject', 'foo', 'bar');
            $t->addDataToObjectType('testobject', 'foo2', '"What?!"');
            $t->addDataToObjectType('testobject2', 'foo3', '!');

            $this->assertEquals('data-foo="bar" data-foo2="\"What?!\""', $t->getDataHTMLAttributesForObjectType('testobject'));

        }

    }

}
