<?php

    /**
     * Utility methods for handling external web services
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Webservice extends \Idno\Common\Component
        {

            /**
             * Send a web services request to a specified endpoint
             * @param string $verb The verb to send the request with; one of POST, GET, DELETE, PUT
             * @param string $endpoint The URI to send the request to
             * @param mixed $params Optionally, an array of parameters to send (keys are the parameter names), or the raw body text (depending on Content-Type)
             * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
             * @return array
             */
            static function send($verb, $endpoint, $params = null, array $headers = null)
            {
                $req = "";
                if ($params && is_array($params)) {
                    $req = http_build_query($params);
                }
                if ($params && !is_array($params))
                    $req = $params;

                $curl_handle = curl_init();

                switch (strtolower($verb)) {
                    case 'post':
                        curl_setopt($curl_handle, CURLOPT_POST, 1);
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $req);
                        $headers[] = 'Expect:';
                        break;
                    case 'delete':
                        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Override request type
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $req);
                        break;
                    case 'put':
                        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT'); // Override request type
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $req);
                        break;
                    case 'get':
                    default:
                        curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
                        if (strpos($endpoint, '?') !== false) {
                            $endpoint .= '&' . $req;
                        } else {
                            $endpoint .= '?' . $req;
                        }
                        break;
                }

                curl_setopt($curl_handle, CURLOPT_URL, $endpoint);
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl_handle, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_handle, CURLOPT_USERAGENT, "Known http://withknown.com");
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
                
                // Proxy connection string provided
                if (!empty(\Idno\Core\site()->config()->proxy_string)) {
                    curl_setopt($curl_handle, CURLOPT_PROXY, \Idno\Core\site()->config()->proxy_string);
                }

                // Allow plugins and other services to extend headers, allowing for plugable authentication methods on calls
                $new_headers = \Idno\Core\site()->triggerEvent('webservice:headers', ['headers' => $headers, 'verb' => $verb]);
                if (!empty($new_headers) && (is_array($new_headers))) {
                    if (empty($headers)) $headers = [];
                    $headers = array_merge($headers, $new_headers);
                }

                if (!empty($headers) && is_array($headers)) {
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
                }

                $buffer      = curl_exec($curl_handle);
                $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

                if ($error = curl_error($curl_handle)) {
                    \Idno\Core\site()->logging->log($error, LOGLEVEL_ERROR);
                }

                curl_close($curl_handle);

                return ['content' => $buffer, 'response' => $http_status, 'error' => $error];
            }

            /**
             * Send a web services GET request to a specified URI endpoint
             * @param string $endpoint The URI to send the GET request to
             * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
             * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
             * @return array
             */
            static function get($endpoint, array $params = null, array $headers = null)
            {
                return self::send('get', $endpoint, $params, $headers);
            }

            /**
             * Send a web services POST request to a specified URI endpoint
             * @param string $endpoint The URI to send the POST request to
             * @param mixed $params Optionally, an array of parameters to send (keys are the parameter names), or the raw body text (depending on Content-Type)
             * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
             * @return array
             */
            static function post($endpoint, $params = null, array $headers = null)
            {
                return self::send('post', $endpoint, $params, $headers);
            }

            /**
             * Send a web services PUT request to a specified URI endpoint
             * @param string $endpoint The URI to send the PUT request to
             * @param mixed $params Optionally, an array of parameters to send (keys are the parameter names), or the raw body text (depending on Content-Type)
             * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
             * @return array
             */
            static function put($endpoint, $params = null, array $headers = null)
            {
                return self::send('put', $endpoint, $params, $headers);
            }

            /**
             * Send a web services DELETE request to a specified URI endpoint
             * @param string $endpoint The URI to send the DELETE request to
             * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
             * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
             * @return array
             */
            static function delete($endpoint, array $params = null, array $headers = null)
            {
                return self::send('delete', $endpoint, $params, $headers);
            }

            /**
             * Replacement for file_get_contents for retrieving remote files.
             * Essentially a wrapper for self::get()
             * @param type $url
             */
            static function file_get_contents($url)
            {
                $result = self::get($url);

                if ($result['error'] == "")
                    return $result['content'];

                return false;
            }
        }

    }