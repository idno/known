<?php

    /**
     * Swaps access credentials with a hub
     */

    namespace Idno\Pages\Hub\Register {

        class User extends \Idno\Common\Page
        {

            function post()
            {

                $this->flushBrowser();

                \Idno\Core\Idno::site()->logging->debug("Loading the user registration callback");

                $contents   = $this->getInput('content');
                $auth_token = $this->getInput('auth_token');
                $time       = $this->getInput('time');
                $signature  = $this->getInput('signature');

                $secret = \Idno\Core\Idno::site()->hub()->secret;

                $hmac = hash_hmac('sha1', $contents . $time . $auth_token, $secret);

                if ($hmac == $signature) {

                    if ($contents = json_decode($contents)) {
                        if (!empty($contents->user)) {
                            if ($user = \Idno\Entities\User::getByUUID($contents->user)) {
                                $user->hub_settings = array('token' => $contents->auth_token, 'secret' => $contents->secret);
                                $user->save();
                                $result = array('status' => 'ok', 'message' => 'Credentials were stored.');
                            } else {
                                $result = array('status' => 'fail', 'message' => 'Couldn\'t find user: ' . $contents->user);
                            }
                        } else {
                            $result = array('status' => 'fail', 'message' => 'No user was sent');
                        }
                    } else {
                        $result = array('status' => 'fail', 'message' => 'Contents were invalid');
                    }

                }

                if (empty($result)) {
                    $result = array('status' => 'fail', 'message' => 'Signature does not match: ' . $signature . ', ' . $hmac);
                }

                echo json_encode($result);
                exit;

            }

        }

    }
