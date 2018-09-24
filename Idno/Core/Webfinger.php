<?php

    /**
     * Service discovery (via webfinger) class
     *
     * @package idno
     * @subpackage core
     */

namespace Idno\Core {

    class Webfinger extends \Idno\Common\Component
    {

        function init()
        {
        }

        function registerpages()
        {
            site()->addPageHandler('/\.well\-known/webfinger/?', '\Idno\Pages\Webfinger\View');
        }

    }

}

