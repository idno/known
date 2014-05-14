<?php

    /**
     * Service discovery (via webfinger) class
     *
     * @package known
     * @subpackage core
     */

    namespace known\Core {

        class Webfinger extends \known\Common\Component
        {

            function init()
            {
            }

            function registerpages()
            {
                site()->addPageHandler('/\.well\-known/webfinger/?', '\known\Pages\Webfinger\View');
            }

        }

    }