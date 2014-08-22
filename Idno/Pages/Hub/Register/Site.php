<?php

    /**
     * Swaps access credentials with a hub
     */

    namespace Idno\Pages\Hub\Register {

        class Site extends \Idno\Common\Page
        {

            function post()
            {

                \Idno\Core\site()->logging->log('Site registration message received', LOGLEVEL_DEBUG);

                $token      = $this->getInput('token');
                $auth_token = $this->getInput('auth_token');
                $secret     = $this->getInput('secret');

                $match_token = \Idno\Core\site()->hub()->getRegistrationToken();

                if (empty($token) || empty($auth_token) || empty($secret)) {

                    $result = ['status' => 'fail', 'message' => 'Empty credentials.'];

                }
                if ($match_token == $token) {

                    \Idno\Core\site()->hub()->saveDetails($auth_token, $secret);
                    $result = ['status' => 'ok', 'message' => 'Credentials were stored.'];

                } else {

                    $result = ['status' => 'fail', 'message' => 'Request token does not match'];

                }

                echo json_encode($result);
                exit;

            }

        }

    }
