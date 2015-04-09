<?php

    namespace IdnoPlugins\Convoy\Pages {

        use Idno\Common\Page;

        class Token extends Page {

            function getContent() {

                $this->adminGatekeeper();
                if ($convoy_token = $this->getInput('convoy_token') && $request_token = $this->getInput('request_token')) {
                    if ($verify_request_token = \Idno\Core\site()->session()->get('convoy_request_token')) {
                        $convoy = \Idno\Core\site()->plugins()->get('Convoy'); /* @var \IdnoPlugins\Convoy\Main $convoy */
                        $convoy->saveConvoyToken($convoy_token);
                    }
                }
                $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'settings/connect/');

            }

        }

    }