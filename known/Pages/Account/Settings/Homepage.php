<?php

    /**
     * Change content types that are displayed on a user's homepage
     */

    namespace known\Pages\Account\Settings {

        /**
         * Default class to serve the homepage
         */
        class Homepage extends \known\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); // Logged-in only please
                $t                        = \known\Core\site()->template();
                $t->content_types         = \known\Common\ContentType::getRegistered();
                $t->default_content_types = \known\Core\site()->session()->currentUser()->settings['default_feed_content'];
                $t->body                  = $t->draw('account/settings/homepage');
                $t->title                 = 'Homepage settings';
                $t->drawPage();
            }

            function postContent()
            {
                $this->gatekeeper(); // Logged-in only please
                $user = \known\Core\site()->session()->currentUser();

                $default_feed_content = $this->getInput('default_feed_content');
                if (empty($default_feed_content) || !is_array($default_feed_content)) {
                    $default_feed_content = false;
                }

                $settings                         = $user->settings;
                $settings['default_feed_content'] = $default_feed_content;
                $user->settings                   = $settings;

                if ($user->save()) {
                    \known\Core\site()->session()->addMessage("Your details were saved.");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }