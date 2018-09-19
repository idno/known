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
         * @param array $params Optional params
         * @return \DOMElement
         */
        public function rssSerialise(array $params = []);
        
    }

}
