<?php

/**
 * Utility methods for handling external web services
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Webservice extends \Idno\Common\Component {

        static function post($endpoint, array $params = null) {

            $req = "";
            if ($params) {
                foreach ($params as $key => $value)
                    $req .= "&" . urlencode($key) . "=" . urlencode($value);
            }


            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $endpoint);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, "idno webservice client");
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $req);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);

            $buffer = curl_exec($curl_handle);
            $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            curl_close($curl_handle);

            return ['content' => $buffer, 'response' => $http_status];
        }

        static function get($endpoint, array $params = null) {
            $req = "";
            if ($params) {
                foreach ($params as $key => $value)
                    $req .= "&" . urlencode($key) . "=" . urlencode($value);
            }
            if ($req)
                $endpoint.="?$req";

            $curl_handle = curl_init($endpoint);
            curl_setopt($curl_handle, CURLOPT_HEADER, 0);
            curl_setopt($curl_handle, CURLOPT_VERBOSE, 0);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, "idno webservice client");
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
            $content = curl_exec($curl_handle);
            if (!$content)
                error_log(curl_error($curl_handle));
            $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
            curl_close($curl_handle);
            return ['content' => $content, 'response' => $http_status];
        }

    }

}