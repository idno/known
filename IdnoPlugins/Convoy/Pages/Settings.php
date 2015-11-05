<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Settings extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();

                \Idno\Core\Idno::site()->session()->refreshSessionUser(\Idno\Core\Idno::site()->session()->currentUser());

                $tries = 0;
                if (\Idno\Core\Idno::site()->hub()) {
                    if ($link = \Idno\Core\Idno::site()->hub()->getRemoteLink('hub/connect/settings/link', \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/settings/services')) {
                        $this->forward($link); exit;
                    } else {
                        if ($tries < 5) {
                            \Idno\Core\Idno::site()->hub()->registerUser();
                            sleep(1);
                            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'withknown/settings');
                            $tries++;
                        }
                    }
                }

            }

            function postContent() {
                $this->getContent();
            }

        }

    }