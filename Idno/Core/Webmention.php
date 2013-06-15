<?php

    /**
     * Content announcement (via webmention) class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Webmention extends \Idno\Common\Component
        {

            function init()
            {
            }

            function registerPages() {
                \Idno\Core\site()->addPageHandler('/webmention/?','\Idno\Pages\Webmentions\Endpoint');
            }

            /**
             * Pings mentions from a given page to any linked pages
             * @param $pageURL Page URL
             * @param string $text The text to mine for links
             * @return int The number of pings that were sent out
             */
            static function pingMentions($pageURL, $text) {
                // Load webmention-client
                require_once \Idno\Core\site()->config()->path . '/external/mention-client/mention-client.php';
                $client = new \MentionClient($pageURL, $text);
                $client->debug = true;
                return $client->sendSupportedMentions();
            }

            /**
             * Retrieve content for a given page
             * @param $url
             * @return mixed
             */
            static function getPageContent($url) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, "Idno (webmentions) 0.1");
                if ($response = curl_exec($ch)) {} else error_log(curl_error($ch));
                curl_close($ch);
                return $response;
            }

            static function process($page, $object) {

            }

            /**
             * Parses a given set of HTML for Microformats 2 content
             * @param $content HTML to parse
             * @return array
             */
            static function parseContent($content) {
                $parser = new \mf2\Parser($content);
                return $parser->parse();
            }

        }

    }