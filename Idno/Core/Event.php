<?php

    /**
     * Event class to handle data transport during event triggering
     *
     * @package idno
     * @subpackage core
     */

namespace Idno\Core {

    class Event extends \Symfony\Component\EventDispatcher\Event
    {

        protected $data = array();
        protected $dispatcher = null;
        protected $response = true;
        protected $forward = '';

        function __construct($data = array())
        {
            $this->data       = $data;
            $this->dispatcher = site()->dispatcher;
        }

        /**
         * Retrieve data associated with an event
         * @return mixed
         */
        function &data()
        {
            return $this->data;
        }

        /**
         * Retrieve the response variable associated with this event
         * @return type
         */
        function &response()
        {
            return $this->response;
        }

        /**
         * Set the response variable associated with this event
         * @param $value
         * @return true|false
         */
        function setResponse($value)
        {
            return $this->response = $value;
        }

        /**
         * Retrieve the variable associated with the URL to forward to
         * (if any) after this event
         * @return type
         */
        function &forward()
        {
            return $this->forward;
        }

        /**
         * Overloading the entity property isset check, so that
         * isset($entity->property) and empty($entity->property)
         * work as expected.
         */

        function __isset($name)
        {
            if (!empty($this->attributes[$name])) return true;

            return false;
        }

    }

}

