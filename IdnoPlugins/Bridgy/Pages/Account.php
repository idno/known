<?php

    namespace IdnoPlugins\Bridgy\Pages {

        use Idno\Common\Page;

        class Account extends Page
        {
            public static $SERVICES = array('twitter', 'facebook');

            function getContent()
            {
                $user = \Idno\Core\Idno::site()->session()->currentUser();
                $vars = array();
                foreach (self::$SERVICES as $service) {
                    if ($user && isset($user->bridgy[$service])) {
                        $bdata = $user->bridgy[$service];
                        $vars[$service.'_enabled'] = isset($bdata['status'])
                            && $bdata['status'] == 'enabled';
                        if (isset($bdata['user'])) {
                            $vars[$service.'_user'] = $bdata['user'];
                        }
                        if (isset($bdata['key'])) {
                            $vars[$service.'_key'] = $bdata['key'];
                        }
                    } else {
                        $vars[$service.'_enabled'] = false;
                    }
                }

                $t = \Idno\Core\Idno::site()->template();
                $t->body = $t->__($vars)->draw('bridgy/account');
                $t->title = 'Interactions';
                $t->drawPage();
            }

        }

    }