<?php

namespace IdnoPlugins\IndiePub\Pages\IndieAuth {

    use Idno\Core\Webservice;
    use Idno\Entities\User;

    class Token extends \Idno\Common\Page
    {

        // GET requests verify a token
        function get($params = array())
        {
            $headers = self::getallheaders();
            if (!empty($headers['Authorization'])) {
                $token = $headers['Authorization'];
                $token = trim(str_replace('Bearer', '', $token));
                $found = self::findUserForToken($token);

                if (!empty($found)) {
                    $this->setResponse(200);
                    echo http_build_query($found['data']);
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
            $redirect_uri = $this->getInput('redirect_uri');
            $state        = $this->getInput('state');
            $client_id    = $this->getInput('client_id');

            $verified = Auth::verifyCode($code, $client_id, $redirect_uri, $state);
            if ($verified['valid']===true) {

                // Get user & existing tokens
                $user             = $verified['user'];
                $indieauth_tokens = $user->indieauth_tokens;
                if (empty($indieauth_tokens)) {
                    $indieauth_tokens = array();
                }

                // Generate access token and save it to the user
                $token                    = md5(rand(0, 99999) . time() . $user->getUUID() . $client_id . $state . rand(0, 999999));
                $indieauth_tokens[$token] = array(
                    'me'           => $verified['me'],
                    'redirect_uri' => $redirect_uri,
                    'scope'        => $verified['scope'],
                    'client_id'    => $client_id,
                    'issued_at'    => time(),
                    'nonce'        => mt_rand(1000000, pow(2, 30))
                );
                $user->indieauth_tokens   = $indieauth_tokens;
                $user->save();
                if (\Idno\Core\Idno::site()->session()->isLoggedOn() && $user->getUUID() == \Idno\Core\Idno::site()->session()->currentUser()->getUUID()) {
                    \Idno\Core\Idno::site()->session()->refreshSessionUser($user);
                }

                // Output to the browser
                $this->setResponse(200);
                header('Content-Type: application/x-www-form-urlencoded');
                echo http_build_query(array(
                    'access_token' => $token,
                    'scope'        => $verified['scope'],
                    'me'           => $verified['me'],
                ));
                exit;

            } else {
                $this->setResponse(400);
                echo $verified['reason'];
            }
        }

        static function findUserForToken($token)
        {
            // find a user by their code
            for ($offset = 0 ; ; $offset += 10) {
                $users = \Idno\Entities\User::get(array(), array(), 10, $offset);
                if (empty($users)) {
                    break;
                }
                foreach ($users as $user) {
                    $indieauth_tokens = $user->indieauth_tokens;
                    if (!empty($indieauth_tokens) && isset($indieauth_tokens[$token])) {
                        return array(
                            'user' => $user,
                            'data' => $indieauth_tokens[$token],
                        );
                    }
                }
            }
            return array();
        }
    }

}
