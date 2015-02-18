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

            function registerEventHooks()
            {

                // Add webmention headers to the top of the page
                \Idno\Core\site()->addEventHook('page/head', function (Event $event) {

                    if (!empty(site()->config()->hub)) {
                        $eventdata = $event->data();
                        header('Link: <' . \Idno\Core\site()->config()->getDisplayURL() . 'webmention/>; rel="http://webmention.org/"', false);
                        header('Link: <' . \Idno\Core\site()->config()->getDisplayURL() . 'webmention/>; rel="webmention"', false);
                    }

                });

            }

            /**
             * Pings mentions from a given page to any linked pages
             * @param $pageURL Page URL
             * @param string $text The text to mine for links
             * @return int The number of pings that were sent out
             */
            static function pingMentions($pageURL, $text)
            {

                if ($current_page = site()->currentPage()) {
                    if ($nowebmention = $current_page->getInput('nomention')) {
                        return true;
                    }
                }

                // Load webmention-client
                require_once \Idno\Core\site()->config()->path . '/external/mention-client-php/src/IndieWeb/MentionClient.php';
                
                // Proxy connection string provided
                $proxystring = false;
                if (!empty(\Idno\Core\site()->config()->proxy_string)) {
                    $proxystring = \Idno\Core\site()->config()->proxy_string;
                }
                
                $client = new \Idno\Core\MentionClient($pageURL, $text, $proxystring);

                return $client->sendSupportedMentions();
            }

            /**
             * Does the supplied page support webmentions?
             * @param $pageURL
             * @param bool $sourceBody
             * @return mixed
             */
            static function supportsMentions($pageURL, $sourceBody = false)
            {
                // Load webmention-client
                require_once \Idno\Core\site()->config()->path . '/external/mention-client-php/src/IndieWeb/MentionClient.php';

                // Proxy connection string provided
                $proxystring = false;
                if (!empty(\Idno\Core\site()->config()->proxy_string)) {
                    $proxystring = \Idno\Core\site()->config()->proxy_string;
                }

                $client = new \Idno\Core\MentionClient($pageURL, $sourceBody, $proxystring);

                return $client->supportsWebmention($pageURL);
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
            static function addSyndicatedReplyTargets($url, $inreplyto = array())
            {
                if (!is_array($inreplyto)) {
                    $inreplyto = array($inreplyto);
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

            /**
             * Given content, returns the type of action you can respond with
             * @param $content
             * @return string
             */
            static function getActionTypeFromHTML($content) {
                $share_type = 'comment';
                if ($mf2 = \Idno\Core\Webmention::parseContent($content['content'])) {
                    if (!empty($mf2['items'])) {
                        foreach ($mf2['items'] as $item) {
                            if (!empty($item['type'])) {
                                if (in_array('h-entry', $item['type'])) {
                                    $share_type = 'reply';
                                }
                                if (in_array('h-event', $item['type'])) {
                                    $share_type = 'rsvp';
                                }
                            }
                        }
                    }
                }
                return $share_type;
            }

            /**
             * Given a URL, returns a user icon (or false)
             * @param $url
             * @return bool|string
             */
            static function getIconFromURL($url) {
                if ($content = Webservice::get($url)) {
                    return self::getIconFromWebsiteContent($content['content'], $url);
                }
                return false;
            }

            /**
             * Retrieve a user's icon from a given homepage
             * @param $content The content of the page
             * @param $url The URL of the page
             * @return $icon_url
             */
            static function getIconFromWebsiteContent($content, $url)
            {
                if ($mf2 = self::parseContent($content, $url)) {
                    $mf2 = (array) $mf2;
                    foreach ($mf2['items'] as $item) {

                        // Figure out what kind of Microformats 2 item we have
                        if (!empty($item['type']) && is_array($item['type'])) {
                            foreach ($item['type'] as $type) {

                                switch ($type) {
                                    case 'h-card':
                                        if (!empty($item['properties'])) {
                                            if (!empty($item['properties']['name'])) $mentions['owner']['name'] = $item['properties']['name'][0];
                                            if (!empty($item['properties']['url'])) $mentions['owner']['url'] = $item['properties']['url'][0];
                                            if (!empty($item['properties']['photo'])) {
                                                //$mentions['owner']['photo'] = $item['properties']['photo'][0];

                                                $tmpfname = tempnam(sys_get_temp_dir(), 'webmention_avatar');
                                                file_put_contents($tmpfname, \Idno\Core\Webservice::file_get_contents($item['properties']['photo'][0]));

                                                $name = md5($item['properties']['url'][0]);

                                                // TODO: Don't update the cache image for every webmention

                                                if ($icon = \Idno\Entities\File::createThumbnailFromFile($tmpfname, $name, 300)) {
                                                    return \Idno\Core\site()->config()->url . 'file/' . (string)$icon;
                                                } else if ($icon = \Idno\Entities\File::createFromFile($tmpfname, $name)) {
                                                    return \Idno\Core\site()->config()->url . 'file/' . (string)$icon;
                                                }

                                                unlink($tmpfname);
                                            }
                                        }
                                        break;
                                }

                            }
                        }

                    }
                }
                return false;
            }

        }

    }
