<?php

/**
 * Tools for Known services.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Service extends \Idno\Common\Component {

        /**
         * Check that a page is being accessed by a local service.
         * This is used to limit access to certain api endpoints to local services (event queue, cron etc).
         * @TODO: Find a cleaner way.
         */
        public static function gatekeeper() {
            $service_signature = $_SERVER['HTTP_X_KNOWN_SERVICE_SIGNATURE'];
            if (empty($service_signature))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Missing X-Known-Service-Signature, service call is not possible.'));

            if ($service_signature != static::generateToken(\Idno\Core\Idno::site()->currentPage()->currentUrl()))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Sorry, signature doesn\'t match up.'));

            return true;
        }

        /**
         * Generate a token based on the site secret and URL.
         * @param type $url Endpoint URL you're calling
         * @return string
         * @throws \Idno\Exceptions\ConfigurationException
         * @throws \RuntimeException
         */
        public static function generateToken($url) {

            $site_secret = \Idno\Core\Idno::site()->config()->site_secret;
            if (empty($site_secret))
                throw new \Idno\Exceptions\ConfigurationException(\Idno\Core\Idno::site()->language()->_('Missing site secret'));

            if (empty($url))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Url not provided to token generation.'));

            return hash_hmac('sha256', $url, $site_secret);
        }
        
        /**
         * Call a service endpoint
         * @param type $endpoint
         * @return boolean
         * @throws \RuntimeException
         */
        public static function call($endpoint, $params = []) {
            
            if (empty($endpoint))
                throw new \RuntimeException('No endpoint given');
            
            if (strpos($endpoint, 'http')===false) // Handle variation in endpoint call
                $endpoint = \Idno\Core\Idno::site()->config()->getDisplayURL() . ltrim($endpoint, '/');
            
            \Idno\Core\Idno::site()->logging()->debug("Calling $endpoint");
            
            $signature = \Idno\Core\Service::generateToken($endpoint);
                            
            if ($result = \Idno\Core\Webservice::get($endpoint, $params, [
                'X-KNOWN-SERVICE-SIGNATURE: ' . $signature
            ])) {
                 $error = $result['response'];
                $content = json_decode($result['content']);
                
                if ($error != 200) {
                                    
                    if (empty($content))
                        throw new \RuntimeException('Response from service endpoint was not json');
                    
                    if (!empty($content->exception->message))
                        throw new \RuntimeException($content->exception->message);
                    
                } else {
                    
                    // Response is ok
                    return $content;
                }
                
            } else {
                throw new \RuntimeException('No result from endpoint.');
            }
             return false;
            
        }

    }

}