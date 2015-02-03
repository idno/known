<?php

/**
 * Known webmention client.
 * 
 * This class extends the IndieWeb webmention client and better integrates it
 * with Known core.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class MentionClient extends \IndieWeb\MentionClient {

        protected static function _get($url) {
            return Webservice::get($url)['content'];
        }

        protected static function _post($url, $body, $headers = array(), $returnHTTPCode = false) {

            $result = Webservice::post($url, $body, $headers);
         
            if ($returnHTTPCode)
                return $result['response'];

            return $result['content'];
        }

        protected function _fetchHead($url) {
            $response = Webservice::get($url);
            if (!empty($response['headers'])) {
                return $this->_parse_headers($response['headers']);
            }
            return [];
        }

        protected function _fetchBody($url) {
            return self::_get($url);
        }

    }

}