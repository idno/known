<?php

    /**
     * Hubs are central, or semi-central servers, that provide functionality for groups of Known users.
     * Functionality may include managed cron jobs, feed parsing, social syndication, discovery, and more.
     *
     */

    namespace Idno\Core {

        use Idno\Entities\User;

        class Hub extends \Idno\Common\Component
        {

            public $server = '';
            public $auth_token = '';
            public $secret = '';
            public $token = '';
            public $client = false;

            function __construct($server)
            {
                parent::__construct();
                $this->setServer($server);
            }

            /**
             * Sets the hub server to connect to
             * @param $server
             */
            function setServer($server)
            {
                $this->server = $server;
            }

            function registerPages()
            {
                // These pages will be called by the hub after registration
                \Idno\Core\Idno::site()->addPageHandler('/hub/register/site/callback/?', 'Idno\Pages\Hub\Register\Site', true);
                \Idno\Core\Idno::site()->addPageHandler('/hub/register/user/callback/?', 'Idno\Pages\Hub\Register\User', true);
            }

            function registerEventHooks()
            {
                // Register user on login
                \Idno\Core\Idno::site()->addEventHook('login/success', function (\Idno\Core\Event $event) {
                    $eventdata = $event->data();
                    if ($user = $eventdata['user']) {
                        $this->registerUser($user);
                    }
                });
            }

            /**
             * Register the current user with the Known hub. The site must have been registered first.
             *
             * @param bool $user
             * @return bool
             */
            function registerUser($user = false)
            {
                if (empty($user)) {
                    $user = \Idno\Core\Idno::site()->session()->currentUser();
                }
                if ($user instanceof User) {
                    $user     = User::getByUUID($user->getUUID());
                    $contents = json_encode($user);
                    $time     = time();
                    $details  = $this->loadDetails();
                    $results  = Webservice::post($this->server . 'hub/user/register', array(
                        'content'    => $contents,
                        'time'       => $time,
                        'auth_token' => $details['auth_token'],
                        'signature'  => hash_hmac('sha1', $contents . $time . $details['auth_token'], $details['secret'])
                    ));

                    if ($results['response'] == 401) {
                        \Idno\Core\Idno::site()->config->hub_settings = array();
                        \Idno\Core\Idno::site()->config->save();
                        $user->hub_settings = array();
                        $user->save();
                        if ($user->getUUID() == \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                            \Idno\Core\Idno::site()->session()->refreshSessionUser($user);
                        }
                    }

                    return true;
                }

                return false;
            }

            /**
             * Load the locally stored auth token & secret details, or register with the hub if no details have been
             * saved
             * @return bool
             */
            function loadDetails()
            {
                if (!empty(\Idno\Core\Idno::site()->config->hub_settings['auth_token']) && !empty(\Idno\Core\Idno::site()->config->hub_settings['secret'])) {
                    $this->setAuthToken(\Idno\Core\Idno::site()->config->hub_settings['auth_token']);
                    $this->setSecret(\Idno\Core\Idno::site()->config->hub_settings['secret']);

                    return \Idno\Core\Idno::site()->config->hub_settings;
                }

                return false;
            }

            /**
             * Sets the public auth token to use to communicate with the hub server
             * @param $token
             */
            function setAuthToken($token)
            {
                $this->auth_token = $token;
            }

            /**
             * Sets the secret auth token to use to communicate with the hub server
             * @param $secret
             */
            function setSecret($secret)
            {
                $this->secret = $secret;
            }

            /**
             * Establish a connection to the server at the specified location.
             *
             * @return bool True on success
             */
            function connect()
            {

                $details = $this->loadDetails();
                if (!empty($details['auth_token'])) {
                    // Apply pre-stored auth details and connect to server
                } else if (
                    !substr_count($_SERVER['REQUEST_URI'], 'callback') &&
                    !substr_count($_SERVER['REQUEST_URI'], '.') &&
                    !substr_count($_SERVER['REQUEST_URI'], '/file/')
                ) {
                    // Establish auth details, save them, and then connect
                    if ($details = $this->register()) {
                    }
                }

                // If we have details, and we're logged in, connect
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    if (!empty($details)) {
                        try {
                            if (!$this->userIsRegistered(\Idno\Core\Idno::site()->session()->currentUser())) {
                                \Idno\Core\Idno::site()->logging->info("User isn't registered on hub; registering ...");
                                $this->registerUser(\Idno\Core\Idno::site()->session()->currentUser());
                            }
                        } catch (\Exception $e) {
                            \Idno\Core\Idno::site()->logging->error('Exception registering user on hub', ['error' => $e]);
                        }
                    }
                }

                return false;
            }

            /**
             * Register this Known site with the Known hub
             *
             * @return bool
             */
            function register()
            {

                if (empty(\Idno\Core\Idno::site()->config->last_hub_ping)) {
                    $last_ping = 0;
                } else {
                    $last_ping = \Idno\Core\Idno::site()->config->last_hub_ping;
                }

                if ($last_ping < (time() - 10)) { // Throttling registration pings to hub

                    $results = Webservice::post($this->server . 'hub/site/register', array(
                        'url'   => \Idno\Core\Idno::site()->config()->getURL(),
                        'title' => \Idno\Core\Idno::site()->config()->getTitle(),
                        'token' => $this->getRegistrationToken()
                    ));

                    if ($results['response'] == 200) {
                        \Idno\Core\Idno::site()->config->load();
                        \Idno\Core\Idno::site()->config->last_hub_ping = time();
                        \Idno\Core\Idno::site()->config->save();

                        return true;
                    }

                }

                return false;
            }

            /**
             * Retrieves a token for use in registering this Known site with a hub. Tokens last for 10 minutes.
             * @return string
             */
            function getRegistrationToken()
            {
                if (empty(\Idno\Core\Idno::site()->config->hub_settings) || !is_array(\Idno\Core\Idno::site()->config->hub_settings)) {
                    \Idno\Core\Idno::site()->config->hub_settings = array();
                }
                if (!empty(\Idno\Core\Idno::site()->config->hub_settings['registration_token'])) {
                    if (!empty(\Idno\Core\Idno::site()->config->hub_settings['registration_token_expiry'])) {
                        if (\Idno\Core\Idno::site()->config->hub_settings['registration_token_expiry'] > (time() - 600)) {
                            return \Idno\Core\Idno::site()->config->hub_settings['registration_token'];
                        }
                    }
                }

                $token_generator      = new TokenProvider();
                $token                = $token_generator->generateToken(32);

                $hextoken = (string) bin2hex($token);

                \Idno\Core\Idno::site()->config->hub_settings = array(
                    'registration_token' => (string) bin2hex($token),
                    'registration_token_expiry' => time()
                );

                \Idno\Core\Idno::site()->config->save();

                return \Idno\Core\Idno::site()->config->hub_settings['registration_token'];
            }

            /**
             * Detect whether the current user has registered with the hub & stored credentials
             * @param bool $user
             * @return bool
             */
            function userIsRegistered($user = false)
            {
                if (empty($user)) {
                    $user = \Idno\Core\Idno::site()->session()->currentUser();
                    \Idno\Core\Idno::site()->session()->refreshSessionUser($user);
                }
                if ($user instanceof User) {
                    if (!empty($user->hub_settings)) {
                        if (!empty($user->hub_settings['token']) && !empty($user->hub_settings['secret'])) {
                            return true;
                        }
                    }
                }

                return false;
            }

            /**
             * Makes a call to the hub
             *
             * @param $endpoint
             * @param $contents
             * @param bool $user
             * @return array|bool
             */
            function makeCall($endpoint, $contents, $user = false)
            {

                if (!$user) {
                    $user = \Idno\Core\Idno::site()->session()->currentUser();
                }

                if ($user instanceof User) {
                    if ($this->userIsRegistered($user)) {
                        $contents = json_encode($contents);
                        $time     = time();
                        $details  = $user->hub_settings;
                        $results  = Webservice::post($this->server . $endpoint, array(
                            'content'    => $contents,
                            'time'       => $time,
                            'auth_token' => $details['token'],
                            'signature'  => hash_hmac('sha1', $contents . $time . $details['token'], $details['secret'])
                        ));

                        return $results;
                    }
                }

                return false;

            }

            /**
             * Retrieves a link that will allow the current user to log into the hub page at $endpoint
             *
             * @param $endpoint
             * @param $callback
             * @return bool|string
             */
            function getRemoteLink($endpoint, $callback)
            {
                $user = \Idno\Core\Idno::site()->session()->currentUser();
                $user = User::getByUUID($user->getUUID());
                \Idno\Core\Idno::site()->session()->refreshSessionUser($user);

                if ($this->userIsRegistered($user)) {
                    if (!empty($user->hub_settings['token'])) {
                        $time      = time();
                        $signature = hash_hmac('sha1', $user->hub_settings['token'] . $time, $user->hub_settings['secret']);

                        return $this->server . $endpoint . '?token=' . urlencode($user->hub_settings['token']) . '&time=' . $time . '&signature=' . $signature . '&callback=' . urlencode($callback);
                    }
                }

                return false;

            }

            /**
             * Save hub auth
             * @param $token
             * @param $secret
             */
            function saveDetails($token, $secret)
            {
                \Idno\Core\Idno::site()->config->load();
                if (!is_array(\Idno\Core\Idno::site()->config->hub_settings)) {
                    \Idno\Core\Idno::site()->config->hub_settings = array();
                }
                \Idno\Core\Idno::site()->config->hub_settings['auth_token'] = $token;
                \Idno\Core\Idno::site()->config->hub_settings['secret']     = $secret;
                \Idno\Core\Idno::site()->config->save();
                $this->setAuthToken($token);
                $this->setSecret($secret);
            }

        }

    }