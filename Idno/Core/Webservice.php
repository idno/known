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

            public static $lastRequest = '';
            public static $lastResponse = '';

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


                $curl_handle = curl_init();

                switch (strtolower($verb)) {
                    case 'post':
                        curl_setopt($curl_handle, CURLOPT_POST, 1);
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect:';
                        break;
                    case 'delete':
                        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Override request type
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $params);
                        break;
                    case 'put':
                        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT'); // Override request type
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $params);
                        break;
                    case 'get':
                    default:
                        $req = "";
                        if ($params && is_array($params)) {
                            $req = http_build_query($params);
                        }
                        if ($params && !is_array($params)) {
                            $req = $params;
                        }

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
                curl_setopt($curl_handle, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_handle, CURLOPT_USERAGENT, "Known https://withknown.com");
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLINFO_HEADER_OUT, 1);
                curl_setopt($curl_handle, CURLOPT_HEADER, 1);
                
                // Allow unsafe ssl verify
                if (!empty(\Idno\Core\site()->config()->disable_ssl_verify)) {
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
                } else {
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2); 
                }
                

                // If we're calling this function as a logged in user, then we need to store cookies in a cookiejar
                if ($user = \Idno\Core\site()->session()->currentUser()) {
                    // Save cookie to user specific cookie jar, using some level of obfuscation
                    curl_setopt($curl_handle, CURLOPT_COOKIEJAR, \Idno\Core\site()->config()->cookie_jar . md5($user->getUUID() . \Idno\Core\site()->config()->site_secret)); 
                }

                // Proxy connection string provided
                if (!empty(\Idno\Core\site()->config()->proxy_string)) {
                    curl_setopt($curl_handle, CURLOPT_PROXY, \Idno\Core\site()->config()->proxy_string);
                    
                    // If proxy type not specified by command string (as some settings can't be), allow for proxy type to be passed.
                    if (!empty(\Idno\Core\site()->config()->proxy_type)) {
                        $type = 0;
                        switch (\Idno\Core\site()->config()->proxy_type) {

                            case 'socks4': 
                            case 'CURLPROXY_SOCKS4': $type = CURLPROXY_SOCKS4; break;

                            case 'socks5': 
                            case 'CURLPROXY_SOCKS5': $type = CURLPROXY_SOCKS5; break;

                            case 'socks5-hostname': 
                            case 'CURLPROXY_SOCKS5_HOSTNAME': $type = 7; break; // Use proxy to resolve DNS, but this isn't defined in current versions of curl

                            case 'http':
                            case 'CURLPROXY_HTTP' : 
                            default : $type = CURLPROXY_HTTP; break;
                        }
                        
                        curl_setopt($curl_handle, CURLOPT_PROXYTYPE, $type);
                    }
                }

                // Allow plugins and other services to extend headers, allowing for plugable authentication methods on calls
                $new_headers = \Idno\Core\site()->triggerEvent('webservice:headers', array('headers' => $headers, 'verb' => $verb));
                if (!empty($new_headers) && (is_array($new_headers))) {
                    if (empty($headers)) $headers = array();
                    $headers = array_merge($headers, $new_headers);
                }

                if (!empty($headers) && is_array($headers)) {
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
                }

                $buffer      = self::webservice_exec($curl_handle);
                $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
                
                // Get HTTP Header / body
                $header_size = curl_getinfo($curl_handle, CURLINFO_HEADER_SIZE);
                $header = substr($buffer, 0, $header_size);
                $content = substr($buffer, $header_size);
                

                if ($error = curl_error($curl_handle)) {
                    \Idno\Core\site()->logging->log($error, LOGLEVEL_ERROR);
                }

                self::$lastRequest  = curl_getinfo($curl_handle, CURLINFO_HEADER_OUT);
                self::$lastResponse = $content;

                curl_close($curl_handle);

                return array('header' => $header, 'content' => $content, 'response' => $http_status, 'error' => $error);
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

            /**
             * Take a URL, check for a schema and add one if necessary
             * @param $url
             * @return string|bool
             */
            static function sanitizeURL($url)
            {
                if (!empty($url)) {
                    if (!substr_count($url, ':') && !substr_count($url, '//')) {
                        $url = 'http://' . $url;
                    }

                    return $url;
                }

                return false;
            }

            /**
             * Retrieves the last HTTP request sent by the service client
             * @return string
             */
            static function getLastRequest()
            {
                return self::$lastRequest;
            }

            /**
             * Retrieves the last HTTP response sent to the service client
             * @return string
             */
            static function getLastResponse()
            {
                return self::$lastResponse;
            }

            /**
             * Wrapper for curl_exec
             * @param $ch
             * @param null $maxredirect
             * @return bool|mixed
             */
            static function webservice_exec($ch, &$maxredirect = null)
            {

                $mr = $maxredirect === null ? 5 : intval($maxredirect);
                $open_basedir = ini_get('open_basedir');

                if (empty($open_basedir)
                    && !filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN)
                ) {

                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                } else {

                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

                    /*if ($mr > 0) {
                        $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                        $newurl       = $original_url;

                        $rch = curl_copy_handle($ch);

                        $post_fields = curl_getinfo($ch, CURLOPT_POSTFIELDS);

                        curl_setopt($rch, CURLOPT_HEADER, true);
                        curl_setopt($rch, CURLOPT_NOBODY, true);
                        curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                        curl_setopt($rch, CURLOPT_POSTFIELDS, $post_fields);

                        do {
                            curl_setopt($rch, CURLOPT_URL, $newurl);
                            site()->session()->addMessage("Checking " . $newurl);
                            $header = curl_exec($rch);
                            if (curl_errno($rch)) {
                                $code = 0;
                            } else {
                                $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                                if ($code == 301 || $code == 302) {
                                    preg_match('/Location:(.*?)\n/i', $header, $matches);
                                    $newurl = trim(array_pop($matches));

                                    // if no scheme is present then the new url is a
                                    // relative path and thus needs some extra care
                                    if (!preg_match("/^https?:/i", $newurl)) {
                                        $newurl = $original_url . $newurl;
                                    }
                                } else {
                                    $code = 0;
                                }
                            }
                        } while ($code && --$mr);

                        curl_close($rch);

                        if (!$mr) {
                            if ($maxredirect === null)
                                trigger_error('Too many redirects.', E_USER_WARNING);
                            else
                                $maxredirect = 0;

                            return false;
                        }
                        curl_setopt($ch, CURLOPT_URL, $newurl);
                    }*/
                }

                return curl_exec($ch);
            }

        }

    }
