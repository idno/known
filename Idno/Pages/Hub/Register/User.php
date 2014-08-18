<?php

    /**
     * Swaps access credentials with a hub
     */

    namespace Idno\Pages\Hub\Register {

        class User extends \Idno\Common\Page
        {

            function post()
            {

                $contents   = $this->getInput('contents');
                $auth_token = $this->getInput('auth_token');
                $time       = $this->getInput('time');
                $signature  = $this->getInput('signature');

                $secret = \Idno\Core\site()->hub()->secret;

                $hmac = hash_hmac('sha1', $contents . $time . $auth_token, $secret);

                if ($hmac == $signature) {

                    if ($contents = json_decode($contents)) {
                        if (!empty($contents->user)) {
                            if ($user = \Idno\Entities\User::getByID($contents->user)) {
                                $user->hub_token  = $contents->auth_token;
                                $user->hub_secret = $contents->secret;
                                $user->save();
                                $result = ['status' => 'ok', 'message' => 'Credentials were stored.'];
                            }
                        }
                    }

                }

                if (empty($result)) {
                    $result = ['status' => 'fail', 'message' => 'Request token does not match'];
                }

                echo json_encode($result);
                exit;

            }

        }

    }
