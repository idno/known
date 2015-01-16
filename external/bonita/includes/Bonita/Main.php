<?php

	/**
	
		Bonita static management class file
		
		@package Bonita
	
	 */
	 
	 namespace Bonita {
		 class Main {

			/** Configuration variables **/

				static $path = '';						// The path of the Bonita base
				static $additionalPaths = array();		// Additional paths to check for templates
				static $cache = false;					// Set depending on the existence of the cache
				static $secret = '';					// Site secret

			/** Private helper vars **/

				private static $templates = array();	// Template overrides

			/** Useful functions **/

				/**
				 * Returns whether or not we're running off the cache
				 * @return true|false
				 */

					static function cached() {
						return self::$cache;
					}

				/**
				 * Sets an additional path to check for (eg) templates
				 * Does nothing if we're running off the cache
				 * @param string $path A full path
				 * @return true|false Depending on success
				 */

					static function additionalPath($path) {
						if (self::cached()) return false;
						if (!empty($path) && is_dir($path)) {
							if (!in_array($path,self::$additionalPaths)) {
								array_unshift(self::$additionalPaths,$path);
							}
						}
					}

				/**
				 * Get any saved additional paths (or an empty array if there aren't any)
				 * @return array
				 */

					static function getAdditionalPaths() {
						return self::$additionalPaths;
					}

				/**
				 * Gets all saved paths, including the main Bonita path
				 * @return array
				 */

					static function getPaths() {
						$paths = self::getAdditionalPaths();
						$paths[] = self::$path;
						return $paths;
					}

				/**
				 * Sets the site secret
				 */

					public static function siteSecret($secret) {
						self::$secret = $secret;
					}

				/**
				 * Retrieves the site secret
				 * @return string The site secret
				 */

					public static function getSiteSecret() {
						return self::$secret;
					}

				/**
				 * Returns a string identifier describing the user's device OS, based on the
				 * browser string. (Default: "default")
				 *
				 * @return string A string describing the device OS, or "default" by default
				 */

					static function detectDevice() {
						if (empty($_SERVER['HTTP_USER_AGENT'])) return 'default';
						$ua = $_SERVER['HTTP_USER_AGENT'];

						// Android
						if (preg_match('/android/i',$ua)) return 'android';

						// iOS devices
						if (preg_match('/ipad/i',$ua)) return 'ipad';
						if (preg_match('/ipod/i',$ua) || preg_match('/iphone/i',$ua)) return 'iphone';

						// Blackberry (WebKit and older)
						if (preg_match('/blackberry/i',$ua)) {
							if (!preg_match('/webkit/i',$ua)) return 'blackberry';
							return 'blackberry-webkit';
						}

						// Windows Phone
						if (preg_match('/windows phone/i',$ua)) return 'windows-phone';

						// Windows Mobile
						if (preg_match('/windows ce/i',$ua)) return 'windows-mobile';

						// Opera Mini
						if (preg_match('/opera mini/i',$ua)) return 'opera-mini';

						// Opera
						if (preg_match('/opera/i',$ua)) return 'opera';

						// Internet Explorer
						if (preg_match('/msie/i',$ua)) return 'msie';

						return 'default';
					}

		 }
	 }
