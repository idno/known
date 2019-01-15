<?php

namespace Idno\Entities {

    use Idno\Common\Component;

    class UnfurledUrl extends BaseObject
    {

        /**
         * Copied and modified from https://github.com/mapkyca/php-ogp, extract information from graph headers
         * @param type $content
         */
        private static function parseHeaders($content)
        {

            require_once \Idno\Core\Idno::site()->config()->path . '/external/php-ogp/ogp/Parser.php';

            return \ogp\Parser::parse($content);
        }

        /**
         * Basic security, don't allow every domain to push oembed.
         */
        public function isOEmbedWhitelisted()
        {

            $host = parse_url($this->source_url, PHP_URL_HOST);

            if (!empty($host)) {
                $host = str_replace('www.', '', $host);
                return in_array($host, [
                    'youtube.com',
                    'youtu.be',
                    'instagram.com',
                    'soundcloud.com',
                    'twitter.com',
                    'vimeo.com',
                    'amazon.com',
                    'amazon.co.uk',
                ]);
            }

            return false;
        }

        /**
         * Unfurl and unpack a url, extracting title, description, open-graph and oembed
         * @param type $url
         */
        public function unfurl($url)
        {

            $url = trim($url);
            $unfurled = [];
            
            if (!filter_var($url, FILTER_VALIDATE_URL)) 
                return false;

            $contents = \Idno\Core\Webservice::file_get_contents($url);
            if (!empty($contents)) {

                // Extract OpenGraph/Facebook/Twitter stuff
                $graphheaders = self::parseHeaders($contents);
                if (!empty($graphheaders)) {

                    // Unfurled url
                    $unfurled = array_merge($unfurled, $graphheaders);

                    // See if there's any mf2 in content
                    $parser = new \Mf2\Parser($contents, $url);
                    try {
                        $mf2 = $parser->parse();
                        if (!empty($mf2))
                            $unfurled['mf2'] = $mf2;
                    } catch (\Exception $e) {
                        \Idno\Core\Idno::site()->logging()->debug($e->getMessage());
                    }
                }

                $this->data = $unfurled;
                $this->source_url = $url;

                return true;
            }

            return false;
        }

        public static function getBySourceURL($url)
        {
            return static::getOne(['source_url' => $url]);
        }

    }

}
