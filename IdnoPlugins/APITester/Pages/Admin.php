<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace IdnoPlugins\APITester\Pages {

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
                    \Idno\Core\site()->session()->set('api_request',false);
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
                    $body = \Idno\Core\site()->template()->__([
                        'request'      => $request,
                        'key'          => $key,
                        'username'     => $username,
                        'json'         => $json,
                        'sent_request' => $sent_request,
                        'response'     => $response
                    ])->draw('apitester/admin');
                } else {
                    $body = \Idno\Core\site()->template()->draw('apitester/nocurl');
                }

                \Idno\Core\site()->template()->__([
                    'title' => "API Tester",
                    'body'  => $body,
                ])->drawPage();

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
                $url              = \Idno\Core\site()->config()->url;
                if (strripos($url, '/') == strlen($url) - 1) {
                    $url = substr($url, 0, strlen($url) - 1);
                }
                $url .= $request;

                if (is_callable('curl_init')) {
                    $ch = \curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_POST           => true, // Make a POST call
                        CURLOPT_HEADER         => true, // Keep headers in the response
                        CURLOPT_HTTPHEADER     => [
                            'X-KNOWN-USERNAME: ' . $username,
                            'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', $request, $key, true)),
                            'User-Agent: Known http://withknown.com'
                        ],
                        CURLOPT_POSTFIELDS     => trim($json),
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLINFO_HEADER_OUT    => true
                    ]);
                    if (!empty($follow_redirects)) {
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    }
                    $response     = curl_exec($ch);
                    $sent_request = curl_getinfo($ch, CURLINFO_HEADER_OUT);
                    curl_close($ch);

                    $api_request = [
                        'request'      => $request,
                        'key'          => $key,
                        'username'     => $username,
                        'json'         => $json,
                        'sent_request' => $sent_request,
                        'response'     => $response
                    ];

                    \Idno\Core\site()->session()->set('api_request', $api_request);

                } else {
                    \Idno\Core\site()->session()->addMessage('The API Tester can\'t make an API call without the curl extension.');
                }

                $this->forward(\Idno\Core\site()->config()->url . 'admin/apitester/');

            }
        }
    }