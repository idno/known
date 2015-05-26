<?php

    namespace IdnoPlugins\IndiePub\Pages\IndieAuth {

        use Idno\Core\Webservice;
        use Idno\Entities\User;

        class Token extends \Idno\Common\Page
        {

            // GET requests verify a token
            function get()
            {

                $headers = getallheaders();
                $user    = User::getOne();

                if (!empty($headers['Authorization'])) {
                    $token            = $headers['Authorization'];
                    $token            = trim(str_replace('Bearer', '', $token));
                    $indieauth_tokens = $user->indieauth_tokens;
                    if (!empty($indieauth_tokens[$token])) {
                        $this->setResponse(200);
                        echo http_build_query($indieauth_tokens[$token]);
                        exit;
                    }
                }
                $this->setResponse(404);
                echo "Client mismatch.";

            }

            // POST requests generate a token
            function post()
            {

                // Get parameters
                $code         = $this->getInput('code');
                $me           = $this->getInput('me');
                $redirect_uri = $this->getInput('redirect_uri');
                $state        = $this->getInput('state');
                $client_id    = $this->getInput('client_id');

                // Verify code
                $response = Webservice::post('https://indieauth.com/auth', array(
                    'me'           => $me,
                    'code'         => $code,
                    'redirect_uri' => $redirect_uri,
                    'state'        => $state,
                    'client_id'    => $client_id
                ));
                if ($response['response'] == 200) {
                    parse_str($response['content'], $content);
                    if (!empty($content['me']) && parse_url($content['me'], PHP_URL_HOST) == parse_url(\Idno\Core\site()->config()->getURL(), PHP_URL_HOST)) {

                        // Get user & existing tokens
                        $user             = \Idno\Entities\User::getOne(array('admin' => true));
                        $indieauth_tokens = $user->indieauth_tokens;
                        if (empty($indieauth_tokens)) {
                            $indieauth_tokens = array();
                        }

                        // Generate access token and save it to the user
                        $token                    = md5(rand(0, 99999) . time() . $user->getUUID() . $client_id . $state . rand(0, 999999));
                        $indieauth_tokens[$token] = array(
                            'me'           => $me,
                            'redirect_uri' => $redirect_uri,
                            'scope'        => 'post',
                            'client_id'    => $client_id,
                            'issued_at'    => time(),
                            'nonce'        => mt_rand(1000000, pow(2, 30))
                        );
                        $user->indieauth_tokens   = $indieauth_tokens;
                        $user->save();
                        if (\Idno\Core\site()->session()->isLoggedOn() && $user->getUUID() == \Idno\Core\site()->session()->currentUser()->getUUID()) {
                            \Idno\Core\site()->session()->refreshSessionUser($user);
                        }

                        // Output to the browser
                        $this->setResponse(200);
                        header('Content-Type: application/x-www-form-urlencoded');
                        echo http_build_query(array(
                            'access_token' => $token,
                            'scope'        => 'post',
                            'me'           => $me,
                        ));
                        exit;

                    } else {

                        $this->setResponse(404);
                        echo "Client mismatch.";

                    }
                }

            }

        }

    }
