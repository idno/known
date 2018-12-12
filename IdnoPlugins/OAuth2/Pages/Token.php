<?php

namespace IdnoPlugins\OAuth2\Pages {

    class Token extends \Idno\Common\Page
    {

        function getContent()
        {

            try {
                try {
                    $scope = $this->getInput('scope');
                    $state = $this->getInput('state');
                    $code = $this->getInput('code');
                    $grant_type = $this->getInput('grant_type');
                    $client_id = $this->getInput('client_id');
                    $redirect_uri = $this->getInput('redirect_uri');

                    if (!$grant_type)
                    {
                        throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Required parameter grant_type is missing!"), 'invalid_request', $state);
                    }

                    switch ($grant_type) {

                        // Refresh token
                        case 'refresh_token' :

                            $refresh_token = $this->getInput('refresh_token');

                            if (!$refresh_token)
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Required parameter refresh_token is missing!"), 'invalid_request', $state);
                            }

                            if (!($token = \IdnoPlugins\OAuth2\Token::getOne([/*'key' => $client_id, */'refresh_token' => $refresh_token]))) 
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Sorry, that refresh token appears to be invalid!"), 'invalid_grant', $state);
                            }

                            // Check state on object
                            if ($token->state) {
                                if ($token->state != $state) 
                                {
                                    throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Invalid state given"), 'access_denied', $state);
                                }
                            }

                            // OK so far, so generate new token
                            $newtoken = new \IdnoPlugins\OAuth2\Token();

                            // Add state and scope variables
                            $newtoken->state = $token->state;
                            $newtoken->scope = $token->scope;

                            // Bind to a client ID!
                            $newtoken->key = $token->key;

                            // Set owner from code object
                            $newtoken->setOwner($token->getOwner());

                            // Ok, delete old token and issue a new token
                            if ($token->delete() && $newtoken->save()) {
                                echo json_encode($newtoken);
                            }
                            else
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Server problem, couldn't refresh token. Try again in a bit..."), 'invalid_grant', $state);
                            }

                            break;

                        // Basic authorisation
                        case 'authorization_code':
                        default:

                            if (!$client_id)
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Required parameter client_id is missing!"), 'invalid_request', $state);
                            }

                            // Check Application
                            if (!\IdnoPlugins\OAuth2\Application::getOne(['key' => $client_id]))
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("I have no knowledge of the application identified by %s", [$client_id]), 'unauthorized_client', $state);
                            }

                            // Check code
                            if ((!($code_obj = \IdnoPlugins\OAuth2\Code::getOne(['code' => $code, 'key' => $client_id]))) || ($code_obj->expires < time()))
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Sorry, unknown or expired code!"), 'invalid_grant', $state);
                            }

                            // Check state on object
                            if ($code_obj->state) {
                                if ($code_obj->state != $state)
                                {
                                    throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Invalid state given"), 'access_denied', $state);
                                }
                            }

                            // Check redirect
                            if ($code_obj->redirect_uri) {
                                if ($code_obj->redirect_uri != $redirect_uri)
                                {
                                    throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Sorry, redirect_uri doesn't match the one given before!"), 'access_denied', $state);
                                }
                            }

                            // OK so far, so generate new token
                            $token = new \IdnoPlugins\OAuth2\Token();

                            // Add state and scope variables
                            $token->state = $state;
                            $token->scope = $code_obj->scope;

                            // Bind to a client ID!
                            $token->key = $client_id;

                            // Set owner from code object
                            $token->setOwner($code_obj->getOwner());

                            if (!$token->save())
                            {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Server problem, couldn't generate new tokens. Try again in a bit..."), 'invalid_grant', $state);
                            }

                            echo json_encode($token);
                    }
                } catch (\IdnoPlugins\OAuth2\OAuth2Exception $oa2e) {
                    $this->setResponse($oa2e->http_code);
                    echo json_encode($oa2e->jsonSerialize());
                }
            } catch (\Exception $e) {
                $this->setResponse(400);

                echo json_encode([
                    'error' => 'invalid_request',
                    'error_description' => $e->getMessage()
                ]);
            }
        }

        function postContent()
        {
            $this->getContent();
        }

    }

}