<?php

    /**
     * Hubs are central, or semi-central servers, that provide functionality for groups of Known users.
     * Functionality may include managed cron jobs, feed parsing, social syndication, discovery, and more.
     *
     */

    namespace Idno\Core {

        class Hub extends \Idno\Common\Component {

            public $server = '';
            public $auth_token = '';
            public $secret = '';
            public $token = '';

            function __construct($server) {
                parent::__construct();
                $this->setServer($server);
                $this->connect();
            }

            function registerPages() {
                // This page will be called by the hub after registration
                site()->addPageHandler('hub/register', 'Idno\Pages\Hub\Register', true);
            }

            /**
             * Sets the hub server to connect to
             * @param $server
             */
            function setServer($server) {
                $this->server = $server;
            }

            /**
             * Sets the public auth token to use to communicate with the hub server
             * @param $token
             */
            function setAuthToken($token) {
                $this->auth_token = $token;
            }

            /**
             * Sets the secret auth token to use to communicate with the hub server
             * @param $secret
             */
            function setSecret($secret) {
                $this->secret = $secret;
            }

            /**
             * Establish a connection to the server at the specified location.
             *
             * @return bool True on success
             */
            function connect() {
                if ($details = $this->loadDetails()) {

                    // Apply pre-stored auth details and connect to server

                } else {

                    // Establish auth details, save them, and then connect
                    if ($details = $this->register()) {

                    }

                }
                return false;
            }

            /**
             * Retrieves a token for use in registering this Known site with a hub. Tokens last for 10 minutes.
             * @return string
             */
            function getRegistrationToken() {
                if (empty(site()->config->hub_settings)) {
                    site()->config->hub_settings = [];
                }
                if (!empty(site()->config->hub_settings['registration_token'])) {
                    if (!empty(site()->config->hub_settings['registration_token_expiry'])) {
                        if (site()->config->hub_settings['registration_token_expiry'] > (time() - 600)) {
                            return site()->config->hub_settings['registration_token'];
                        }
                    }
                }
                $token_generator = new \OAuthProvider();
                $token = $token_generator->generateToken(32);
                $config = site()->config;
                $config->hub_settings['registration_token'] = bin2hex($token);
                $config->hub_settings['registration_token_expiry'] = time();
                $config->save();
                site()->config = $config;
                return site()->config->hub_settings['registration_token'];
            }

            /**
             * Register this Known site with the Known hub
             *
             * @return bool
             */
            function register() {
                $web_client = new Webservice();
                $results = $web_client->post($this->server . 'hub/site/register',[
                    'url' => site()->config()->getURL(),
                    'token' => $this->getRegistrationToken()
                ]);
                return false;
            }

            /**
             * Load the locally stored auth token & secret details, or register with the hub if no details have been
             * saved
             * @return bool
             */
            function loadDetails() {
                // Get details
                // Then set them to the data structure
                return false;
            }

        }

    }