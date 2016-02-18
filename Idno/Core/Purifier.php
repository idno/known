<?php

    namespace Idno\Core {

        class Purifier extends \Idno\Common\Component
        {

            protected $purifier;

            function init()
            {

                $config = \HTMLPurifier_Config::createDefault();
                $config->set('Cache.DefinitionImpl', null);
                $this->purifier = new \HTMLPurifier($config);

            }

            function registerEventHooks()
            {
                \Idno\Core\Idno::site()->addEventHook('text/filter', function (\Idno\Core\Event $event) {

                    $text = $event->response();

                    $text = $this->purify($text);

                    $event->setResponse($text);
                });
            }

            function purify($html)
            {

                return $this->purifier->purify($html);

            }
        }

    }