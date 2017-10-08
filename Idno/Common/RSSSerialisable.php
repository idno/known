<?php

/**
 * Describe an interface for outputting something as an RSS compatible DOM.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Common {

    interface RSSSerialisable {
        
        /**
         * Serialise an item into a rss compatible DOMElement.
         * @param \DOMDocument $page DOMDocument passed to this entity
         * @param array $params Optional params
         * @return \DOMElement
         */
        public function rssSerialise(\DOMDocument &$page, array $params = []);
        
    }

}