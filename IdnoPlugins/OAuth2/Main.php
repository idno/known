<?php

namespace IdnoPlugins\OAuth2 {

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

            // Adding OAuth2 app page
            \Idno\Core\site()->routes()->addRoute('/account/oauth2/?', '\IdnoPlugins\OAuth2\Pages\Account\Applications');
            \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/oauth2/menu');
        }

        function registerEventHooks()
        {

            // Authenticate!
            \Idno\Core\site()->events()->addListener('user/auth/request', function(\Idno\Core\Event $event) {
                if ($user = \IdnoPlugins\OAuth2\Main::authenticate())
                $event->setResponse($user);

            }, 0);
        }
        
        private static function getBearerToken(): ?string {
            
            $headers = null;
            $serverheaders = \Idno\Common\Page::getallheaders();
            
            if ($serverheaders['Authorization'])
                $headers = trim($serverheaders["Authorization"]);
            else if (isset($serverheaders['HTTP_AUTHORIZATION']))
                $headers = trim($serverheaders["HTTP_AUTHORIZATION"]);
            
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    return trim($matches[1], '\'"');
                }
            }
                
            return null;
        }
        
        public static function authenticate()
        {
            $access_token = \Idno\Core\Input::getInput('access_token');
            if (!$access_token)
                $access_token = self::getBearerToken ();
            
            // Have we been provided with an access token
            if ($access_token) {

                \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);

                // Get token
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
                            if ($owner->oauth2[$token->key]['scope'] != $token->scope)
                            throw new \Exception(\Idno\Core\Idno::site()->language()->_("Token scope doesn't match that which was previously granted!"));

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
