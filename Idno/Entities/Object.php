<?php

    /**
     * User-created object representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        /**
         * @deprecated Object is a reserved word in PHP 7.2+ and will be removed in the next version, use BaseObject instead.
         */
        abstract class Object extends \Idno\Common\Entity
        {

            function __construct() {
                
                \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: Object is a reserved word in PHP 7.2+ and will be removed in the next version, use BaseObject instead.");
                
                parent::__construct();
            }
        }

    }