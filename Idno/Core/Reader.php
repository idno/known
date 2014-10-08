<?php

    namespace Idno\Core {

        use Idno\Common\Component;
        use Idno\Entities\Reader\FeedItem;
        use Idno\Entities\Reader\Subscription;
        use Idno\Entities\User;

        class Reader extends Component
        {

            // Register pages
            function registerPages() {
                site()->addPageHandler('/following/?', '\Idno\Pages\Following\Home');
                site()->addPageHandler('/following/add/?', '\Idno\Pages\Following\Add');
                site()->addPageHandler('/stream/?', '\Idno\Pages\Stream\Home');
            }

            /**
             * Given the content of a page and its URL, returns an array of FeedItem objects (or false on failure)
             * @param $content
             * @param $url
             * @return array|bool
             */
            function parseFeed($content, $url)
            {

                // Try XML (RSS or Atom)
                $xml_parser = new \SimplePie();
                $xml_parser->set_raw_data($content);
                $xml_parser->init();
                if (!$xml_parser->error()) {

                    return $this->xmlFeedToFeedItems($xml_parser->get_items(), $url);

                }

                // Check for microformats
                if ($html = @\DOMDocument::loadHTML($content)) {
                    try {
                        $parser  = new \Mf2\Parser($html, $url);
                        $parsed_content = $parser->parse();
                        return $this->mf2FeedToFeedItems($parsed_content, $url);
                    } catch (\Exception $e) {
                        return false;
                    }
                }

                return false;

            }

            /**
             * Given a parsed microformat feed, returns an array of FeedItem objects
             * @param $mf2_content
             * @param $url
             * @return array
             */
            function mf2FeedToFeedItems($mf2_content, $url)
            {

                $items = [];
                if (!empty($mf2_content['items'])) {
                    foreach ($mf2_content['items'] as $item) {
                        if (in_array('h-entry', $item['type'])) {
                            $entry = new FeedItem();
                            $entry->loadFromMF2(array($item));
                            $entry->setFeedURL($url);
                            $items[] = $entry;
                        }
                    }
                }

                return $items;

            }

            /**
             * Given a parsed RSS or Atom feed, returns an array of FeedItem objects
             * @param $rss_content
             * @param $url
             * @return array
             */
            function xmlFeedToFeedItems($xml_items, $url)
            {

                $items = [];
                if (!empty($xml_items)) {
                    foreach ($xml_items as $item) {

                        $entry = new FeedItem();
                        $entry->loadFromXMLItem($item);
                        $entry->setFeedURL($url);
                        $items[] = $entry;

                    }
                }

                return $items;

            }

            /**
             *
             * @param $url
             * @return array|bool
             */
            function fetchAndParseFeed($url)
            {

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    return false;
                }
                $client = new Webservice();
                if ($result = $client->get($url)) {
                    return $this->parseFeed($result['content'], $url);
                }

                return false;

            }

            /**
             * Given the URL of a website, returns a single URL of a feed (whether Microformats or RSS / Atom). The
             * function will attempt to discover RSS and Atom feeds in the page if this is an HTML site. Returns false
             * if there is no feed.
             * @param $url
             * @return mixed
             */
            function getFeedURL($url)
            {

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    return false;
                }

                $client = new Webservice();
                if ($result = $client->get($url)) {

                    if (!empty($result['content'])) {
                        if ($html = @\DOMDocument::loadHTML($result['content'])) {
                            $xpath = new \DOMXpath($html);
                            if ($xpath->query("//*[contains(concat(' ', @class, ' '), ' h-entry ')]")->length > 0) {
                                return $url;
                            }
                            if ($rss_url = $this->findXMLFeedURL($html)) {
                                return $rss_url;
                            }
                        }
                        if ($xml = @simplexml_load_string($result['content'])) {
                            if (!empty($xml->channel->item) || !empty($xml->feed)) {
                                return $url;
                            }
                        }
                    }

                }

                return false;

            }

            /**
             * Given the content of a web page, returns the URL of a linked-to RSS or Atom feed
             * @param $content
             * @return array|bool
             */
            function findXMLFeedURL($html)
            {

                $xpath = new \DOMXPath($html);
                $feeds = $xpath->query("//head/link[@href][@type='application/rss+xml']/@href");

                if ($feeds->length > 0) {
                    foreach ($feeds as $feed) {
                        return $feed->nodeValue;
                    }
                }

                $feeds = $xpath->query("//head/link[@href][@type='application/atom+xml']/@href");

                if ($feeds->length > 0) {
                    foreach ($feeds as $feed) {
                        return $feed->nodeValue;
                    }
                }

                return false;

            }

            /**
             * Retrieve a particular user's subscriptions
             * @param $user
             * @return array|bool
             */
            function getUserSubscriptions($user) {

                if ($user instanceof User) {
                    $user = $user->getUUID();
                }
                if (empty($user)) {
                    return false;
                }

                return Subscription::get(['owner' => $user]);

            }

        }

    }