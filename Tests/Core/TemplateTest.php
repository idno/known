<?php

    namespace Tests\Core {

        use Idno\Core\Template;

        class TemplateTest extends \Tests\KnownTestCase {

            function testParseURLs()
            {
                // adapted test cases from brevity (Known requires the http(s) prefix)
                $testcases = [
                    [
                        "expected" => "This links to a weird domain <a href=\"http://deals.blackfriday\" target=\"_blank\" >http://<wbr />deals.blackfriday</a>.",
                        "text"     => "This links to a weird domain http://deals.blackfriday."
                    ], [
                        "expected" => "<a href=\"http://starts.with.a.link\" target=\"_blank\" >http://<wbr />starts.with.a.link</a> and ends with <a href=\"https://kylewm.com/about#me\">HTML</a>.",
                        "text"     => "http://starts.with.a.link and ends with <a href=\"https://kylewm.com/about#me\">HTML</a>."
                    ], [
                        "expected" => "a matched parenthesis: <a href=\"http://wikipedia.org/Python_(programming_language)\" target=\"_blank\" >http://<wbr />wikipedia.org/<wbr />Python_(programming_language)</a> and (an unmatched parenthesis <a href=\"https://en.wikipedia.org/wiki/Guido_van_Rossum\" target=\"_blank\" >https://<wbr />en.wikipedia.org/<wbr />wiki/<wbr />Guido_van_Rossum</a>)",
                        "text"     => "a matched parenthesis: http://wikipedia.org/Python_(programming_language) and (an unmatched parenthesis https://en.wikipedia.org/wiki/Guido_van_Rossum)"
                    ],
                ];

                $t = new Template();

                foreach ($testcases as $testcase) {
                    $this->assertEquals($testcase['expected'], $t->parseURLs($testcase['text']));
                }

            }

        }

    }
