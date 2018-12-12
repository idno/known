<?php

namespace IdnoPlugins\OAuth2 {

    class OAuth2Exception extends \Exception
    implements \JsonSerializable
    {

        public $http_code;
        public $state;
        public $error;

        public function __construct($message, $error = 'invalid_request', $state = null, $http_code = 400)
        {
            parent::__construct($message);
            $this->error = $error;
            $this->state = $state;
            $this->http_code = $http_code;
        }

        public function jsonSerialize()
        {
            $return =[
            'error' => $this->error,
            'error_description' => $this->getMessage(),

            ];
            if ($this->state) $return['state'] = $this->state;

            return $return;
        }

    }
}
