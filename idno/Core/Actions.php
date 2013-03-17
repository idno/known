<?php

    /**
     * Action management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Actions extends \Bonita\Forms {
		
		/**
		 * Creates an action link that will submit via POST to the page
		 * specified at $pageurl with the data specified in $data
		 * 
		 * @param string $pageurl URL of the page to point to
		 * @param string $label The text of the link
		 * @param array $data Array of name:value pairs that will be submitted to $pageurl
		 * @param array $options Array of options for future use (optional)
		 * @return string
		 */
		function createLink($pageurl, $label, $data = array(), $options = array()) {
		    $data = array_merge($data, $options);
		    return site()->template()->__(array('url' => $pageurl, 'label' => $label, 'data' => $data))->draw('forms/link');
		}
		
	    }
	    
	}

