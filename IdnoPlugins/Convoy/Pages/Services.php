<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Services extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();
                if (\Idno\Core\Idno::site()->hub()) {

                    \Idno\Core\Idno::site()->template()->__([
                        'title' => 'Connect Social Media',
                        'body' => \Idno\Core\Idno::site()->template()->draw('convoy/account/services')
                    ])->drawPage();

                } else if (\Idno\Core\Idno::site()->session()->isAdmin()) {

                    \Idno\Core\Idno::site()->template()->__([
                        'title' => 'Connect Social Media',
                        'body' => \Idno\Core\Idno::site()->template()->draw('convoy/account/signup')
                    ])->drawPage();

                } else {

                    $this->deniedContent();

                }
            }

        }

    }