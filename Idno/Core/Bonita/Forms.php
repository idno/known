<?php

/**
 * Bonita form handling class.
 *
 * Uses the templating class and some extra helpers to provide a safe form handler. See
 * examples/forms.php for more.
 *
 * @package    Bonita
 * @subpackage Forms
 */

namespace Idno\Core\Bonita {

    class Forms extends Templates
    {

        /**
         *  Attaches a target URL to the form.
         *
         * @param string $targetURL URL for the form to point to
         */
        public function setTarget($targetURL)
        {
            $this->targetURL = $targetURL;
        }

        public function draw($templateName, $returnBlank = true)
        {

            $time = time();
            $this->token = static::token($this->targetURL, $time);
            $this->time = $time;
            parent::draw($templateName, $returnBlank);
        }

        /**
         *  Gatekeeper function that validates input forms and prevents csrf attacks.
         *  Call this from your form action code.
         *
         * @param  string  $targetURL                 The URL of the form action that brought us here.
         * @param  boolean $haltExecutionOnBadRequest If set to true, the function halts all execution if the form doesn't validate. (True by default.)
         * @return true|false
         */
        public static function validateToken($action = '', $haltExecutionOnBadRequest = true)
        {
            if (!\Idno\Core\Idno::site()->request()->request->has('__bTs') || !\Idno\Core\Idno::site()->request()->request->has('__bTk')) {
                if ($haltExecutionOnBadRequest) {
                    exit;
                }

                return false;
            }
            $time = \Idno\Core\Idno::site()->request()->request->get('__bTs');
            $token = \Idno\Core\Idno::site()->request()->request->get('__bTk');
            if (empty($action)) {
                if (\Idno\Core\Idno::site()->request()->request->has('__bTa')) {
                    $action = \Idno\Core\Idno::site()->request()->request->get('__bTa');
                } else {

                    \Idno\Core\Idno::site()->logging()->debug("No action in token");

                    if ($haltExecutionOnBadRequest) {
                        exit;
                    }

                    return false;
                }
            }

            if (abs(time() - $time) < \Idno\Core\Idno::site()->config()->form_token_expiry) {

                // \Idno\Core\Idno::site()->logging()->debug("Token for $action has a valid time " . date('r', $time));

                if (self::token($action, $time) == $token) {
                    return true;
                }
            } else {
                \Idno\Core\Idno::site()->logging()->debug("Token for $action has expired - ts = " . date('r', $time));
            }
            if ($haltExecutionOnBadRequest) {
                exit;
            }

            return false;
        }

        /**
         *  Determines whether Bonita form submission data exists and is ready to be processed.
         *
         * @return true|false
         */
        public static function formSubmitted()
        {

            if (\Idno\Core\Idno::site()->request()->request->has('__bTk') && \Idno\Core\Idno::site()->request()->request->has('__bTs')) {
                return true;
            }

            return false;
        }

        /**
         *    Generate a token based on a given action and UNIX timestamp.
         *
         * @param string $targetURL The URL of the form action we're using.
         * @param int    $time      The current timestamp.
         *
         * @return true|false
         */
        public static function token($action, $time)
        {

            // Normalise action by stripping get line
            $action = explode('?', $action)[0];

            $hmac = hash_hmac('sha256', $action, \Idno\Core\Bonita\Main::getSiteSecret(), true);
            $hmac = hash_hmac('sha256', $time, $hmac, true);
            $hmac = hash_hmac('sha256', session_id(), $hmac);

            $debug = [
                'action' => $action,
                'time' => $time,
                'site_secret' => \Idno\Core\TokenProvider::truncateToken(\Idno\Core\Idno::site()->config()->site_secret),
                'session_id' => \Idno\Core\TokenProvider::truncateToken(session_id()),
                'token' => \Idno\Core\TokenProvider::truncateToken($hmac),
            ];
            // \Idno\Core\Idno::site()->logging()->debug("Generating CSRF token for {$action} over: ". print_r($debug, true));

            return $hmac;
        }

        /**
         * Obfuscate a form field name.
         *
         * @param  type $field
         * @return type
         */
        public static function obfuscateField($field)
        {
            return md5(\Idno\Core\Idno::site()->config()->site_secret . $field);
        }

    }

}
