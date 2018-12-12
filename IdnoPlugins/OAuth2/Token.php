<?php

namespace IdnoPlugins\OAuth2 {


    class Token extends \Idno\Common\Entity
    {

        function __construct($token_type = 'grant', $expires_in = 2419200)
        {

            parent::__construct();

            $this->access_token = hash('sha256', mt_rand() . microtime(true));
            $this->refresh_token = hash('sha256', mt_rand() . microtime(true));
            $this->expires_in = $expires_in; // Default expires is 1 month, like facebook
            $this->token_type = $token_type;

            $this->setTitle($this->access_token); // better stub generation, not that it matters
        }

        /**
         * Check whether a token is valid (i.e. not expired) and that an application with the given key exists.
         */
        function isValid()
        {

            if (!\IdnoPlugins\OAuth2\Application::getOne(['key' => $this->key])) return false;
            return ($this->created + $this->expires_in > time());
        }

        /**
         * Saves changes to this object based on user input
         * @return true|false
         */
        function saveDataFromInput()
        {

            if (empty($this->_id)) {
                $new = true;
            } else {
                $new = false;
            }

            $this->setAccess('PUBLIC');
            return $this->save();
        }

        function jsonSerialize()
        {
            // Code is only ever serialised as part of something else
            $return = [
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires_in' => $this->expires_in,
            'token_Type' => $this->token_type
            ];

            if ($this->state) $return['state'] = $this->state;
            if ($this->scope) $return['scope'] = $this->scope;

            return $return;
        }


    }

}
