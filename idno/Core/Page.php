<?php

    /**
     * Handles pages in the system (and, by extension, the idno API).
     * 
     * Developers shoudld extend the getContent, postContent and dataContent 
     * methods as follows:
     * 
     * getContent: echoes HTML to the page
     * 
     * postContent: handles content submitted to the page (assuming that form
     * elements were correctly signed)
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Page extends \Idno\Common\Component {
		
		/**
		 * Internal function used to handle GET requests.
		 * Performs some administration functions and hands off to
		 * getContent().
		 */
		function get() {
		    site()->session()->APIlogin();
		    $this->getContent();
		}
		
		/**
		 * Internal function used to handle POST requests.
		 * Performs some administration functions, checks for the 
		 * presence of a POST token, and hands off to postContent().
		 * 
		 * @param $forward boolean If this is set to true, forward the page; otherwise return data.
		 */
		function post($forward = true) {
		    if (Action::validateToken('', false)) {
			site()->session()->APIlogin();
			$this->postContent($forward);
		    }
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest requests.
		 * Sets the template to JSON and then calls get().
		 */
		function get_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->get();
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest requests.
		 * Sets the template to JSON and then calls post().
		 */
		function post_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->post();
		}
		
		/**
		 * To be extended by developers
		 */
		function getContent() {}
		
		/**
		 * To be extended by developers
		 */
		function postContent($forward = true) {}
		
	    }
	    
	}

