<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Connect extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();
                if (\Idno\Core\Idno::site()->hub()) {
                    if ($link = \Idno\Core\Idno::site()->hub()->getRemoteLink('hub/connect/link', \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/settings/services/')) {
                        \Idno\Core\Idno::site()->logging()->debug("Got remote link, forwarding to $link");
                        $this->forward($link); exit;
                    } else {
                        \Idno\Core\Idno::site()->logging()->error("Can't create link");
                        $this->forward($_SERVER['HTTP_REFERER']);
                    }
                } else {
                    \Idno\Core\Idno::site()->logging()->error("Problem, no hub defined.");
                }

            }

            function postContent() {
                $this->getContent();
            }

        }

    }