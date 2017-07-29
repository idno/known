<?php

namespace Idno\Core {

    use Idno\Common\Component;

    class Url extends Component {

        /**
         * Copied and modified from https://github.com/mapkyca/php-ogp, extract information from graph headers
         * @param type $content
         */
        private static function parseHeaders($content) {
            $doc = new \DOMDocument();
            @$doc->loadHTML($content);

            $interested_in = ['og', 'fb', 'twitter']; // Open graph namespaces we're interested in (open graph + extensions)

            $ogp = [];

            // Open graph
            $metas = $doc->getElementsByTagName('meta');
            if (!empty($metas)) {
                for ($n = 0; $n < $metas->length; $n++) {

                    $meta = $metas->item($n);

                    foreach (array('name', 'property') as $name) {
                        $meta_bits = explode(':', $meta->getAttribute($name));
                        if (in_array($meta_bits[0], $interested_in)) {

                            // If we're adding to an existing element, convert it to an array
                            if (isset($ogp[$meta->getAttribute($name)]) && (!is_array($ogp[$meta->getAttribute($name)])))
                                $ogp[$meta_bits[0]][$meta->getAttribute($name)] = array($ogp[$meta->getAttribute($name)], $meta->getAttribute('content'));
                            else if (isset($ogp[$meta->getAttribute($name)]) && (is_array($ogp[$meta->getAttribute($name)])))
                                $ogp[$meta_bits[0]][$meta->getAttribute($name)][] = $meta->getAttribute('content');
                            else
                                $ogp[$meta_bits[0]][$meta->getAttribute($name)] = $meta->getAttribute('content');
                        }
                    }
                }
            }

            // OEmbed
            $metas = $doc->getElementsByTagName('link');
            if (!empty($metas)) {
                for ($n = 0; $n < $metas->length; $n++) {

                    $meta = $metas->item($n);
                    
                    if (strtolower($meta->getAttribute('rel')) == 'alternate') {
                        
                        if (in_array(strtolower($meta->getAttribute('type')), ['application/json+oembed'])) {
                            $ogp['oembed']['json'][] = $meta->getAttribute('href');
                        }
                        if (in_array(strtolower($meta->getAttribute('type')), ['text/xml+oembed'])) {
                            $ogp['oembed']['xml'][] = $meta->getAttribute('href');
                        }
                    }
                }
            }

            // Basics
            foreach (['title'] as $basic) {
                if (preg_match("#<$basic>(.*?)</$basic>#siu", $content, $matches))
                    $ogp[$basic] = trim($matches[1], " \n");
            }

            return $ogp;
        }

        /**
         * Unfurl and unpack a url, extracting title, description, open-graph and oembed
         * @param type $url
         */
        public static function unfurl($url) {

            $unfurled = [];

            $contents = Webservice::file_get_contents($url);
            if (!empty($contents)) {

                // Extract OpenGraph/Facebook/Twitter stuff
                $graphheaders = self::parseHeaders($contents);
                if (!empty($graphheaders)) {
                    
                    // Tag on URL
                    $unfurled['url'] = $url;
                    
                    // Unfurled url
                    $unfurled = array_merge($unfurled, $graphheaders);
                
                
                }
            }

            return $unfurled;
        }

    }

}