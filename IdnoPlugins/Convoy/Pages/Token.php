<?php

    namespace IdnoPlugins\Convoy\Pages {

        use Idno\Common\Page;

        class Token extends Page {

            function getContent() {

                $this->adminGatekeeper();
                if ($convoy_token = $this->getInput('convoy_token')) {
                    $convoy = \Idno\Core\Idno::site()->plugins()->get('Convoy'); /* @var \IdnoPlugins\Convoy\Main $convoy */
                    $convoy->saveConvoyToken($convoy_token);
                }
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/settings/services/');

            }

        }

    }