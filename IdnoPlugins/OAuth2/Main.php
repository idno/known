<?php

namespace IdnoPlugins\OAuth2 {
    
    use \Idno\Entities\User;

    class Main extends \Idno\Common\Plugin
    {

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'oauth2', dirname(__FILE__) . '/languages/'
                )
            );
        }

        function registerPages()
        {
            \Idno\Core\site()->routes()->addRoute('/oauth2/authorise/?', '\IdnoPlugins\OAuth2\Pages\Authorisation');
            \Idno\Core\site()->routes()->addRoute('/oauth2/access_token/?', '\IdnoPlugins\OAuth2\Pages\Token');
            \Idno\Core\site()->routes()->addRoute('/oauth2/connect/?', '\IdnoPlugins\OAuth2\Pages\Connect');
            \Idno\Core\site()->routes()->addRoute('/oauth2/owner/?', '\IdnoPlugins\OAuth2\Pages\Owner');
            
            // Expose some information (public key)
            \Idno\Core\site()->routes()->addRoute('/oauth2/([A-Za-z0-9]+)/?', Pages\Application::class);
            \Idno\Core\site()->routes()->addRoute('/oauth2/([A-Za-z0-9]+)/key/?', Pages\PublicKey::class);

            // Adding OAuth2 app page
            \Idno\Core\site()->routes()->addRoute('/account/oauth2/?', '\IdnoPlugins\OAuth2\Pages\Account\Applications');
            \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/oauth2/menu');
            
            // Expose a well known
            \Idno\Core\site()->routes()->addRoute('/.well-known/openid-configuration/?', Pages\WellKnown::class);
        }

        function registerEventHooks()
        {

            // Authenticate!
            \Idno\Core\site()->events()->addListener('user/auth/request', function(\Idno\Core\Event $event) {
                
                if ($user = \IdnoPlugins\OAuth2\Main::authenticate()) {
                    $event->setResponse($user);
                }

            }, 0);
        }
        
        /**
         * Retrieve Well Known OpenID Connect information.
         * @return array
         */
        public static function getWellKnown(): array {
            return [
                "issuer" => \Idno\Core\Idno::site()->config()->getDisplayURL(),
                "authorization_endpoint" => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/authorise/',
                "token_endpoint" => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/access_token/',
                "userinfo_endpoint" => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/owner/',
                "end_session_endpoint" => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/logout/', 
                
                //"jwks_uri" => "",
                "grant_types_supported" => [
                    "authorization_code",
                    "refresh_token",
                ],
                "response_types_supported" => [
                    "code"
                ],
                "subject_types_supported" => [
                    "public"
                ],
                "id_token_signing_alg_values_supported" => [
                    "RS256"
                ],
//                "response_modes_supported" => [
//                    "query"
//                ]
            ];
        }
        
        public static function authenticate()
        {
            $access_token = \Idno\Core\Input::getInput('access_token');
            if (!$access_token)
                $access_token = \Idno\Common\Page::getBearerToken ();
            
            // Have we been provided with an access token
            if ($access_token) {

                \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);
                
                // Validate bearer if it's a JWT/OIDC
                if (OIDCToken::isJWT($access_token)) {
                
                    // Preliminary decode - peek at the OIDC, to see if we can find the client
                    $unsafejwt = OIDCToken::decodeNoVerify($access_token);
                    
                    if (!empty($unsafejwt->aud)) {
                        
                        // Get the issuing application
                        $application = Application::getOne(['key' => $unsafejwt->aud]);
                        if (!empty($application) && !empty($application->getPublicKey())) {
                        
                            // Now, lets validate.
                            $safejwt = OIDCToken::decode($access_token, $application->getPublicKey());
                            
                            // Ok, we got here, so the OIDC token is valid, lets find a user
                            if (!empty($safejwt)) {
                                
                                $id = $safejwt->sub;
                                $owner = User::getByID($id);
                                
                                if ($owner) {
                                    
                                    \Idno\Core\site()->session()->refreshSessionUser($owner); // Log user on, but avoid triggering hook and going into an infinite loop!

                                    return $owner;
                                    
                                }
                                
                            }
                        
                        }
                        
                    }
                    
                }
                

                // Traditional token
                if ($token = Token::getOne(['access_token' => $access_token])) {

                    // Check expiry
                    if ($token->isValid()) {

                        // Token still valid, get the owner
                        $owner = $token->getOwner();

                        if ($owner) {

                            \Idno\Core\site()->session()->refreshSessionUser($owner); // Log user on, but avoid triggering hook and going into an infinite loop!

                            // Save session scope
                            $_SESSION['oauth2_token'] = $token;

                            // Double check scope
                            if ($owner->oauth2[$token->key]['scope'] != $token->scope) {
                                throw new \Exception(\Idno\Core\Idno::site()->language()->_("Token scope doesn't match that which was previously granted!"));
                            }
                                
                            return $owner;

                        } else {
                            \Idno\Core\site()->events()->triggerEvent('login/failure', array('user' => $owner));

                            throw new \Exception(\Idno\Core\Idno::site()->language()->_("Token user could not be retrieved."));
                        }
                    } else {
                        throw new \Exception(\Idno\Core\Idno::site()->language()->_("Access token %s has expired.", [$access_token]));
                    }
                } else {
                    \Idno\Core\Idno::site()->logging()->debug(\Idno\Core\Idno::site()->language()->_("Access token %s does not match any stored token.", [$access_token]));
                }
            }
        }

    }

}
