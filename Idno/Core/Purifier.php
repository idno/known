<?php

    namespace Idno\Core {

        class Purifier extends \Idno\Common\Component {

            protected $purifier;

            function init() {

                $config = \HTMLPurifier_Config::createDefault();
                $config->set('Cache.DefinitionImpl', null);
                $this->purifier = new \HTMLPurifier($config);

            }

            function purify($html) {

                return $this->purifier->purify($html);

            }

            function registerEventHooks() {
                \Idno\Core\site()->addEventHook('text/filter',function(\Idno\Core\Event $event) {
                    
		    $text = $event->response();
		    
		    $text = $this->purify($text);
		    
		    $event->setResponse($text);
                });
            }
        }

    }