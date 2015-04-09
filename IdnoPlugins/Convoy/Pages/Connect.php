<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Connect extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();
                if (\Idno\Core\site()->hub()) {
                    if ($link = \Idno\Core\site()->hub()->getRemoteLink('hub/connect/link', \Idno\Core\site()->config()->getDisplayURL() . 'account/settings/services/')) {
                        $this->forward($link); exit;
                    } else {
                        error_log("Can't create link");
                        $this->forward($_SERVER['HTTP_REFERER']);
                    }
                }

            }

            function postContent() {
                $this->getContent();
            }

        }

    }