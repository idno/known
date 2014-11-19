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
                $sent_request = '';
                $response     = '';

                $api_request = \Idno\Core\site()->session()->get('api_request');

                if (!empty($api_request)) {
                    $request      = $api_request['request'];
                    $key          = $api_request['key'];
                    $username     = $api_request['username'];
                    $json         = $api_request['json'];
                    $sent_request = $api_request['sent_request'];
                    $response     = $api_request['response'];
                    \Idno\Core\site()->session()->set('api_request', false);
                }

                if (empty($request)) {
                    $request = '/?_t=json';
                }
                if (empty($username)) {
                    $username = \Idno\Core\site()->session()->currentUser()->getHandle();
                }
                if (empty($key)) {
                    $key = \Idno\Core\site()->session()->currentUser()->getAPIkey();
                }
                if (empty($json)) {
                    $json = '[]';
                }

                if (is_callable('curl_init')) {
                    $body = \Idno\Core\site()->template()->__(array(
                        'request'      => $request,
                        'key'          => $key,
                        'username'     => $username,
                        'json'         => $json,
                        'sent_request' => $sent_request,
                        'response'     => $response
                    ))->draw('apitester/admin');
                } else {
                    $body = \Idno\Core\site()->template()->draw('apitester/nocurl');
                }

                \Idno\Core\site()->template()->__(array(
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
                $url              = \Idno\Core\site()->config()->getURL();
                if (strripos($url, '/') == strlen($url) - 1) {
                    $url = substr($url, 0, strlen($url) - 1);
                }
                $url .= $request;

                $client = new Webservice();
                $result = $client->post($url, $json, array(
                    'X-KNOWN-USERNAME: ' . $username,
                    'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', $request, $key, true)),
                ));

                $response     = Webservice::getLastResponse();
                $sent_request = Webservice::getLastRequest() . $json;

                $api_request = array(
                    'request'      => $request,
                    'key'          => $key,
                    'username'     => $username,
                    'json'         => $json,
                    'sent_request' => $sent_request,
                    'response'     => $response
                );
                \Idno\Core\site()->session()->set('api_request', $api_request);

                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/apitester/');

            }
        }
    }