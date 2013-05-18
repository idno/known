<?php

/**
 * Template management class
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Template extends \Bonita\Templates
    {

        /**
         * On construction, detect the template type
         */
        function __construct($template = false)
        {
            if (!($template instanceof Template)) {
                $this->detectTemplateType();
            }
            return parent::__construct($template);
        }

        function autop($html) {
            require_once dirname(dirname(dirname(__FILE__))) . '/external/MrClay_AutoP/AutoP.php';
            $autop = new \MrClay_AutoP();
            return $autop->process($html);
        }

    }

}