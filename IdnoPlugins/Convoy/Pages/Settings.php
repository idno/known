<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Settings extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();

                \Idno\Core\site()->session()->refreshSessionUser(\Idno\Core\site()->session()->currentUser());

                $tries = 0;
                if (\Idno\Core\site()->hub()) {
                    if ($link = \Idno\Core\site()->hub()->getRemoteLink('hub/connect/settings/link', \Idno\Core\site()->config()->getDisplayURL() . 'account/settings/services')) {
                        $this->forward($link); exit;
                    } else {
                        if ($tries < 5) {
                            \Idno\Core\site()->hub()->registerUser();
                            sleep(1);
                            $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'withknown/settings');
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