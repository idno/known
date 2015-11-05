<?php

    /**
     * Webfiiiiinger (da daaaaah dah)
     */

    namespace Idno\Pages\Webfinger {

        /**
         * Default class to serve the homepage
         */
        class View extends \Idno\Common\Page
        {

            function getContent()
            {

                $acct = $this->getInput('resource');
                if (!empty($acct)) {
                    if (substr($acct, 0, 5) == 'acct:' && strlen($acct) > 8) {
                        $handle = str_replace('@' . \Idno\Core\Idno::site()->config()->host, '', substr($acct, 5));
                        if ($user = \Idno\Entities\User::getByHandle($handle)) {
                            $links = \Idno\Core\Idno::site()->triggerEvent('webfinger', array('object' => $user));
                        }
                    }
                }
                if (empty($links)) {
                    $links = array();
                }
                $t = \Idno\Core\Idno::site()->template();
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