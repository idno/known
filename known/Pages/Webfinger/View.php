<?php

    /**
     * Webfiiiiinger (da daaaaah dah)
     */

    namespace known\Pages\Webfinger {

        /**
         * Default class to serve the homepage
         */
        class View extends \known\Common\Page
        {

            function getContent()
            {

                $acct = $this->getInput('resource');
                if (!empty($acct)) {
                    if (substr($acct, 0, 5) == 'acct:' && strlen($acct) > 8) {
                        $handle = str_replace('@' . \known\Core\site()->config()->host, '', substr($acct, 5));
                        if ($user = \known\Entities\User::getByHandle($handle)) {
                            $links = \known\Core\site()->triggerEvent('webfinger', array('object' => $user));
                        }
                    }
                }
                $t = \known\Core\site()->template();
                $t->setTemplateType('json');
                $t->__(array(
                    'subject' => $acct,
                    'links'   => $links
                ))->drawPage();

            }

            function postContent()
            {
            }

        }

    }