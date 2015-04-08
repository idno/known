<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Services extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();
                if (\Idno\Core\site()->hub()) {

                    \Idno\Core\site()->template()->__([
                        'title' => 'Connect Social Media',
                        'body' => \Idno\Core\site()->template()->draw('convoy/account/services')
                    ])->drawPage();

                } else {

                    \Idno\Core\site()->template()->__([
                        'title' => 'Connect Social Media',
                        'body' => \Idno\Core\site()->template()->draw('convoy/account/signup')
                    ])->drawPage();

                }
            }

        }

    }