<?php

namespace Idno\Core {

    use Idno\Entities\User;
    use Idno\Core\Webservice;
    use Idno\Core\Idno;
    
    use Carbon\Carbon;
    
    /**
     * Generate a JWT
     */
    class JWT {
        
        private $user;
        
        private $expiry;
        
        public function __construct($user, int $expiry) {
            $this->user = $user;
            $this->expiry = $expiry;
        }
       
        public function __toString() {
            
            $header = json_encode([
                'typ' => 'JWT',
                'alg' => 'HS256'
            ]);

            $payload = json_encode([
                'user_id' => ($this->user instanceof User) ? $this->user->getOwner()->getID() : $this->user,
                'role' => 'user',
                'exp' => $this->expiry
            ]);
            
            // Encode Header
            $base64UrlHeader = trim(Webservice::base64UrlEncode($header), ',');

            // Encode Payload
            $base64UrlPayload = trim(Webservice::base64UrlEncode($payload), ',');

            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, Idno::site()->config()->site_secret, true);

            // Encode Signature to Base64Url String
            $base64UrlSignature = trim(Webservice::base64UrlEncode($signature), ',');
            
            return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
            
        }
        
        /**
         * Parse a JWT token.
         * H/T https://developer.okta.com/blog/2019/02/04/create-and-verify-jwts-in-php
         * @param string $token
         * @return array|null
         */
        public static function parse(string $token) : ? array {
            
            $tokenParts = explode('.', $token);
            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $signatureProvided = $tokenParts[2];

            // Check token expiry
            $expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
            $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

            // Check signature
            $base64UrlHeader = trim(Webservice::base64UrlEncode($header), ',');
            $base64UrlPayload = trim(Webservice::base64UrlEncode($payload), ',');
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
            $base64UrlSignature = base64UrlEncode($signature);

            // Verify signature
            $signatureValid = ($base64UrlSignature === $signatureProvided);


            if ($tokenExpired) {
                throw new \RuntimeException(Idno::site()->language()->_('Identity token expired'));
            } 
            
            if ($signatureValid) {
                throw new \RuntimeException(Idno::site()->language()->_('Identity token is not valid'));
            } 
            
            return $payload;
        }
    }
    
}