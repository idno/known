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

            protected static function _post($url, $body, $headers = array())
            {
                $response = Webservice::post($url, $body, $headers);
                return [
                    'code'    => $response['response'],
                    'headers' => self::_parse_headers(isset($response['header']) ? $response['header'] : ''),
                    'body'    => $response['content'],
                ];
            }

            protected static function _head($url)
            {
                $response = Webservice::head($url);
                return [
                    'code'    => $response['response'],
                    'headers' => self::_parse_headers(isset($response['header']) ? $response['header'] : ''),
                ];
            }

            protected static function _get($url)
            {
                $response = Webservice::get($url);
                return [
                    'code'    => $response['response'],
                    'headers' => self::_parse_headers(isset($response['header']) ? $response['header'] : ''),
                    'body'    => $response['content'],
                ];
            }

        }

    }
