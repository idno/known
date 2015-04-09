<?php

    /**
     * Swaps access credentials with a hub
     */

    namespace Idno\Pages\Hub\Register {

        class Site extends \Idno\Common\Page
        {

            function post()
            {

                $this->flushBrowser();

                \Idno\Core\site()->logging->log('Site registration message received', LOGLEVEL_DEBUG);

                $token      = $this->getInput('token');
                $auth_token = $this->getInput('auth_token');
                $secret     = $this->getInput('secret');

                $match_token = \Idno\Core\site()->hub()->getRegistrationToken();

                if (empty($token) || empty($auth_token) || empty($secret)) {

                    $result = array('status' => 'fail', 'message' => 'Empty credentials.');

                }
                if ($match_token == $token) {

                    \Idno\Core\site()->hub()->saveDetails($auth_token, $secret);
                    $result = array('status' => 'ok', 'message' => 'Credentials were stored.');

                } else {

                    $result = array('status' => 'fail', 'message' => 'Request token does not match');

                }

                echo json_encode($result);
                exit;

            }

        }

    }
