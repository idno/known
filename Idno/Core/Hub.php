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
                $this->server = $server;
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
                return false;
            }

            /**
             * Register this Known site with the Known hub
             *
             * @return bool
             */
            function register() {
                return false;
            }

            /**
             * Load the locally stored auth token & secret details, or register with the hub if no details have been
             * saved
             * @return bool
             */
            function loadDetails() {
                return false;
            }

        }

    }