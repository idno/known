<?php

namespace IdnoPlugins\OAuth2 {


    class Application extends \Idno\Common\Entity
    {

        /**
         * Return URL of app
         */
        public function getURL() {
            return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/' . $this->key . '/';
        }
        
        /**
         * Generate a new keypair
         */
        public function generateKeypair()
        {
            $this->key = hash('sha256', mt_rand() . microtime(true) . $this->getTitle());
            $this->secret = hash('sha256', mt_rand() . microtime(true) . $this->key);
        }

        /**
         * Helper function to create a new application with a new keypair.
         * @param type $title
         * @return \IdnoPlugins\OAuth2\Application
         */
        public static function newApplication($title)
        {
            $app = new Application();
            $app->setTitle($title);
            $app->generateKeypair();
            $app->generateAsymetricKeypair(); 

            return $app;
        }

        /**
         * Get the public key
         * @return string
         */
        public function getPublicKey():string {
            return $this->publickey;
        }
        
        /**
         * Get the private key
         * @return string
         */
        public function getPrivateKey():string {
            return $this->privatekey;
        }
        
        /**
         * Generate a new public / private key pair suitable for asymetric OIDC tokens
         */
        protected function generateAsymetricKeypair() {
            $config = array(
                "digest_alg" => "sha256",
                "private_key_bits" => 4096,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            );

            // Create the private and public key
            $res = openssl_pkey_new($config);

            // Extract the private key from $res to $privKey
            openssl_pkey_export($res, $privKey);

            // Extract the public key from $res to $pubKey
            $pubKey = openssl_pkey_get_details($res);
            $pubKey = $pubKey["key"];
            
            $this->publickey = $pubKey;
            $this->privatekey = $privKey;
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

            $this->setTitle(\Idno\Core\site()->currentPage()->getInput('name'));

            $this->setAccess('PUBLIC');
            return $this->save();
        }

        function save($overrideAccess = true)
        {
            return parent::save($overrideAccess);
        }

        function jsonSerialize()
        {
            $json = [
                'title' => $this->getTitle(),
                'generated' => $this->getCreatedTime(),
                'client_id' => $this->key,
                
            ];
            
            // If we're logged in and we own this application, we can add some other stuff
            if ($this->canEdit()) {
                $json['secret'] = $this->secret;
            }
            
            $pk = $this->getPublicKey();
            $json['public_key'] = $pk;
            
            return $json;
        }

    }

}
