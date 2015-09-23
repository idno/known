<?php

    namespace Idno\Core {

        class Purifier extends \Idno\Common\Component {

            protected $purifier;

            function __construct() {

                $config = \HTMLPurifier_Config::createDefault();
                $config->set('Cache.DefinitionImpl', null);
                $this->purifier = new \HTMLPurifier($config);

            }

            function purify($html) {

                return $this->purifier->purify($html);

            }

        }

    }