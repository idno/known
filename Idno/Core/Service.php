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

    }

}