<?php

    /**
     * Change content types that are displayed on a user's homepage
     */

    namespace Idno\Pages\Account\Settings {

        /**
         * Default class to serve the homepage
         */
        class Homepage extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t                        = \Idno\Core\site()->template();
                $t->content_types         = \Idno\Common\ContentType::getRegistered();
                $t->default_content_types = \Idno\Core\site()->session()->currentUser()->settings['default_feed_content'];
                $t->body                  = $t->draw('account/settings/homepage');
                $t->title                 = 'Homepage settings';
                $t->drawPage();
            }

            function postContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $user = \Idno\Core\site()->session()->currentUser();

                $default_feed_content = $this->getInput('default_feed_content');
                if (empty($default_feed_content) || !is_array($default_feed_content)) {
                    $default_feed_content = false;
                }

                $settings                         = $user->settings;
                $settings['default_feed_content'] = $default_feed_content;
                $user->settings                   = $settings;

                if ($user->save()) {
                    \Idno\Core\site()->session()->addMessage("Your details were saved.");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }