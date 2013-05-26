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

        }

    }