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

            function registerPages()
            {
                \Idno\Core\site()->addPageHandler('/webmention/?', '\Idno\Pages\Webmentions\Endpoint');
            }

            /**
             * Pings mentions from a given page to any linked pages
             * @param $pageURL Page URL
             * @param string $text The text to mine for links
             * @return int The number of pings that were sent out
             */
            static function pingMentions($pageURL, $text)
            {
                // Load webmention-client
                require_once \Idno\Core\site()->config()->path . '/external/mention-client-php/src/IndieWeb/MentionClient.php';
                
                
                // Proxy connection string provided
                $proxystring = false;
                if (!empty(\Idno\Core\site()->config()->proxy_string)) {
                    $proxystring = \Idno\Core\site()->config()->proxy_string;
                }
                
                $client = new \IndieWeb\MentionClient($pageURL, $text, $proxystring);

                return $client->sendSupportedMentions();
            }

            /**
             * Parses a given set of HTML for Microformats 2 content
             * @param $content HTML to parse
             * @param $url Optionally, the source URL of the content, so relative URLs can be parsed into absolute ones
             * @return array
             */
            static function parseContent($content, $url = null)
            {
                $parser = new \Mf2\Parser($content, $url);
                try {
                    $return = $parser->parse();
                } catch (\Exception $e) {
                    $return = false;
                }

                return $return;
            }

            /**
             * Given an array of URLs (or an empty array) and a target URL to check,
             * adds and rel="syndication" URLs in the target to the array
             * @param $url
             * @param array $inreplyto
             * @return array
             */
            static function addSyndicatedReplyTargets($url, $inreplyto = [])
            {
                if (!is_array($inreplyto)) {
                    $inreplyto = [$inreplyto];
                }
                if ($content = \Idno\Core\Webservice::get($url)) {
                    if ($mf2 = self::parseContent($content['content'], $url)) {
                        $mf2 = (array) $mf2;
                        $mf2['rels'] = (array) $mf2['rels'];
                        if (!empty($mf2['rels']['syndication'])) {
                            if (is_array($mf2['rels']['syndication'])) {
                                foreach ($mf2['rels']['syndication'] as $syndication) {
                                    if (!in_array($syndication, $inreplyto) && !empty($syndication)) {
                                        $inreplyto[] = $syndication;
                                    }
                                }
                            }
                        }
                    }
                }

                return $inreplyto;
            }

        }

    }
