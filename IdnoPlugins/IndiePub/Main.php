<?php

    namespace IdnoPlugins\IndiePub {

        use IdnoPlugins\IndiePub\Pages\IndieAuth\Token;

        class Main extends \Idno\Common\Plugin
        {

            function registerTranslations() {

                \Idno\Core\Idno::site()->language()->register(
                    new \Idno\Core\GetTextTranslation(
                        'indiepub', dirname(__FILE__) . '/languages/'
                    )
                );
            }

            function registerEventHooks()
            {
                \Idno\Core\site()->addEventHook('user/auth/request', function (\Idno\Core\Event $event) {
                    if ($user = \IdnoPlugins\IndiePub\Main::authenticate()) {
                        $event->setResponse($user);
                    }
                });
            }

            function registerPages()
            {
                \Idno\Core\Idno::site()->addPageHandler('/indieauth/auth/?', '\IdnoPlugins\IndiePub\Pages\IndieAuth\Auth', true);
                \Idno\Core\Idno::site()->addPageHandler('/indieauth/approve/?', '\IdnoPlugins\IndiePub\Pages\IndieAuth\Approve', true);
                \Idno\Core\Idno::site()->addPageHandler('/indieauth/token/?', '\IdnoPlugins\IndiePub\Pages\IndieAuth\Token', true);
                \Idno\Core\Idno::site()->addPageHandler('/micropub/endpoint/?', '\IdnoPlugins\IndiePub\Pages\MicroPub\Endpoint', true);
                \Idno\Core\Idno::site()->addPageHandler('/account/indiepub/?', '\IdnoPlugins\IndiePub\Pages\Account');
                \Idno\Core\Idno::site()->addPageHandler('/account/indiepub/revoke/?', '\IdnoPlugins\IndiePub\Pages\Revoke');

                \Idno\Core\Idno::site()->template()->extendTemplate('shell/head', 'indiepub/shell/head');
                \Idno\Core\Idno::site()->template()->extendTemplate('account/menu/items', 'account/menu/items/indiepub');

                header('Link: <' . \Idno\Core\Idno::site()->config()->getURL() . 'indieauth/auth>; rel="authorization_endpoint"', false);
                header('Link: <' . \Idno\Core\Idno::site()->config()->getURL() . 'indieauth/token>; rel="token_endpoint"', false);
                header('Link: <' . \Idno\Core\Idno::site()->config()->getURL() . 'micropub/endpoint>; rel="micropub"', false);
            }

            /**
             * Check that this token is either a user token or the
             * site's API token, and auth the current request for that user if so.
             *
             * @return \Idno\Entities\User user on success
             */
            private static function authenticate()
            {
                $access_token = \Idno\Core\Input::getInput('access_token');
                $headers      = \Idno\Common\Page::getallheaders();
                if (!empty($headers['Authorization'])) {
                    $token = $headers['Authorization'];
                    $token = trim(str_replace('Bearer', '', $token));
                } else if ($token = \Idno\Core\Input::getInput('access_token')) {
                    $token = trim($token);
                }

                if (!empty($token)) {

                    $found = Token::findUserForToken($token);
                    if (!empty($found)) {
                        \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);
                        $user = $found['user'];
                        \Idno\Core\Idno::site()->session()->refreshSessionUser($user);

                        return $user;
                    }
                    $user = \Idno\Entities\User::getOne(array('admin' => true));
                    if ($user)
                        if ($token == $user->getAPIkey()) {
                            \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);
                            \Idno\Core\Idno::site()->session()->refreshSessionUser($user);

                            return $user;
                        }
                }

                return false;
            }


        }

    }