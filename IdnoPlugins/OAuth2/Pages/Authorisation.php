<?php

namespace IdnoPlugins\OAuth2\Pages {

    class Authorisation extends \Idno\Common\Page
    {

        function getContent()
        {
            
            header('Cache-Control: no-store');
            header('Pragma: no-cache');

            try {
                try {
                    $state = $this->getInput('state');
                    $scope = $this->getInput('scope');
                    $response_type = $this->getInput('response_type');
                    $client_id = $this->getInput('client_id');
                    $redirect_uri = $this->getInput('redirect_uri');

                    if (!$response_type) throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Required parameter response_type is missing!"), 'invalid_request', $state);
                    if (!$client_id) throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Required parameter client_id is missing!"), 'invalid_request', $state);

                    switch ($response_type) {

                        case 'token':
                            throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Sorry, implicit grant is currently not supported."), 'unsupported_response_type', $state);
                        break;
                    
                        case 'code':
                        default:
                            // Generate code
                            $code = new \IdnoPlugins\OAuth2\Code();

                            // Save context
                            $code->scope = $scope;
                            $code->key = $client_id;
                            $code->state = $state;
                            $code->redirect_uri = $redirect_uri;

                            // Check Application
                            if (!\IdnoPlugins\OAuth2\Application::getOne(['key' => $client_id])) {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("I have no knowledge of the application identified by %s", [$client_id]), 'unauthorized_client', $state);
                            }

                            // Authenticate user
                            $user = \Idno\Core\site()->session()->currentUser();
                            if (empty($user)) {

                                // Do login and redirect workflow
                                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login?fwd=' . \Idno\Core\Webservice::base64UrlEncode($this->currentUrl()));

                            }

                            // Not authorized before, or change in scope?
                            if ((!$user->oauth2[$client_id]) || ($user->oauth2[$client_id]['scope'] != $scope)) {
                                $this->forward('/oauth2/connect?client_id='.$client_id.'&scope='.urlencode($scope).'&fwd=' . urlencode($this->currentUrl()));
                            }

                            // Check code
                            if ($code->getOne(['code' => $code])) {
                                throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Sorry, this code has been seen before!"), 'access_denied', $state);
                            }

                            // Save code so we've not seen it before
                            if (!$code->save()) throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("Bang, code incorrect"), 'invalid_request', $state);

                            // Forward or echo
                            if ($redirect_uri) {

                                // Normalise url and add parameters
                                if (strpos($redirect_uri, '?')===false)
                                $redirect_uri .= '?';
                                $redirect_uri .= 'code=' . urlencode($code) . '&state=' . urlencode($state);

                                // Forward
                                $this->forward($redirect_uri);

                            } else {
                                // Otherwise echo result
                                echo json_encode([
                                    'code' => $code,
                                    'state' => $state
                                ]);
                            }
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


    }

}
