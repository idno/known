<?php

    /**
     * Base entity interface
     *
     * This is designed to be implemented by anything that needs to be an
     * object in the idno system
     *
     * @package idno
     * @subpackage core
     */

namespace Idno\Common {

    interface EntityInterface extends \JsonSerializable, \ArrayAccess, RSSSerialisable
    {

    }

}

