<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace IdnoPlugins\APITester\Pages {

        use Idno\Core\Webservice;

        /**
         * Default class to serve the homepage
         */
        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->adminGatekeeper();

                $request      = $this->getInput('request');
                $key          = $this->getInput('key');
                $username     = $this->getInput('username');
                $json         = $this->getInput('json');
                $method       = $this->getInput('method', 'GET');
                $sent_request = '';
                $response     = '';

                $api_request = \Idno\Core\Idno::site()->session()->get('api_request');

                if (!empty($api_request)) {
                    $request      = $api_request['request'];
                    $key          = $api_request['key'];
                    $username     = $api_request['username'];
                    $json         = $api_request['json'];
                    $sent_request = $api_request['sent_request'];
                    $response     = gzdecode($api_request['response']);
                    $method       = $api_request['method'];
                    \Idno\Core\Idno::site()->session()->set('api_request', false);
                }

                if (empty($request)) {
                    $request = '/?_t=json';
                }
                if (empty($username)) {
                    $username = \Idno\Core\Idno::site()->session()->currentUser()->getHandle();
                }
                if (empty($key)) {
                    $key = \Idno\Core\Idno::site()->session()->currentUser()->getAPIkey();
                }
                if (empty($json)) {
                    $json = '[]';
                }

                if (is_callable('curl_init')) {
                    $body = \Idno\Core\Idno::site()->template()->__(array(
                        'request'      => $request,
                        'key'          => $key,
                        'username'     => $username,
                        'json'         => $json,
                        'sent_request' => $sent_request,
                        'response'     => $response,
                        'method'       => $method
                    ))->draw('apitester/admin');
                } else {
                    $body = \Idno\Core\Idno::site()->template()->draw('apitester/nocurl');
                }

                \Idno\Core\Idno::site()->template()->__(array(
                    'title' => "API Tester",
                    'body'  => $body,
                ))->drawPage();

                return true;

            }

            function postContent()
            {

                $this->adminGatekeeper();

                $request          = $this->getInput('request');
                $key              = $this->getInput('key');
                $username         = $this->getInput('username');
                $json             = $this->getInput('json');
                $follow_redirects = $this->getInput('follow_redirects');
                $method           = $this->getInput('method', 'GET');
                $url              = \Idno\Core\Idno::site()->config()->getURL();
                if (strripos($url, '/') == strlen($url) - 1) {
                    $url = substr($url, 0, strlen($url) - 1);
                }
                $url .= $request;

                if ($method == 'POST') {
                    $result = Webservice::post($url, $json, array(
                        'X-KNOWN-USERNAME: ' . $username,
                        'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', $request, $key, true)),
                    ));
                } else {
                    $result = Webservice::get($url, null, array(
                        'X-KNOWN-USERNAME: ' . $username,
                        'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', $request, $key, true)),
                    ));
                }

                $response     = Webservice::getLastResponse();
                $sent_request = Webservice::getLastRequest() . $json;

                $api_request = array(
                    'request'      => $request,
                    'key'          => $key,
                    'username'     => $username,
                    'json'         => $json,
                    'sent_request' => $sent_request,
                    'response'     => gzencode($response,9),
                    'method'       => $method
                );
                \Idno\Core\Idno::site()->session()->set('api_request', $api_request);

                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/apitester/');

            }
        }
    }