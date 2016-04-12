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

        class MentionClient extends \IndieWeb\MentionClient
        {

            protected static function _post($url, $body, $headers = array(), $returnHTTPCode = false)
            {

                $result = Webservice::post($url, $body, $headers);

                if ($returnHTTPCode)
                    return $result['response'];

                return $result['content'];
            }

            protected function _head($url)
            {
                $response = Webservice::get($url);

                $result = ['code' => $response['response']];

                if (!empty($response['headers'])) {
                    $result['headers'] =  $this->_parse_headers($response['headers']);
                }

                return $result;
            }

            protected static function _get($url)
            {
                return Webservice::get($url)['content'];
            }

        }

    }