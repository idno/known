<?php

namespace IdnoPlugins\OAuth2;

use Idno\Core\TokenProvider;
use Idno\Core\Webservice;
use Firebase\JWT\JWT;

class OIDCToken {
    
    // JWT Token leeway
    private static $leeway = 10;
    
    /**
     * When given a token, generate an OIDC token from it
     * @param \IdnoPlugins\OAuth2\Token $token
     * @return array
     */
    public static function generate(Token $token) : array {
        
        $nonce = new TokenProvider();
        
        $application = Application::getOne(['key' => $token->key]);
        if (empty($application)) {
            throw new OAuth2Exception(\Idno\Core\Idno::site()->language()->_("The Application for this token could not be found"));
        }
        
        $oidc = [
            'iss' => $application->getURL(), // Issuer site
            'sub' => "" . $token->getOwner()->getID(), // Return the SUBJECT id
            'aud' => $token->key,    // Audience (client ID)
            'exp' => time() + $token->expires_in, // Expires in
            'iat' => time(), // Issue time
            'nonce' => $nonce->generateHexToken(4), // Add a nonce
        ];


        // Have we asked for email address?
        if (strpos($token->scope, 'email') !== false) {
            $oidc['email'] = $token->getOwner()->email;
        } 

        // Add some profile information if asked for
        if (strpos($token->scope, 'profile') !== false) {

            $oidc['preferred_username'] = $token->getOwner()->getHandle();
            $oidc['name'] = $token->getOwner()->getName();
            $oidc['picture'] = $token->getOwner()->getIcon();
            $oidc['profile'] = $token->getOwner()->getURL();
            
            if ($tz = $token->getOwner()->getTimezone()) {
                $oidc['zoneinfo'] = $tz;
            }
        }
        
        return $oidc;
    }
    
    /**
     * Decode a JWT into a usable object.
     * @param string $token
     * @param string $publickey
     * @return object|null
     */
    public static function decode(string $token, string $publickey) : ? object {
        
        list($header, $payload, $signature) = explode(".", $token);
                    
        $plainHeader = Webservice::base64UrlDecode($header);
        $jsonHeader = json_decode($plainHeader, true);

        $algo = ['RS256', $jsonHeader['alg']];

        JWT::$leeway = self::$leeway;
        $result = JWT::decode($token, $publickey, array_unique($algo));
        if ($result) {
            return $result;
        }
        
        return null;
    }
    
    /**
     * Decode the JWT payload WITHOUT verifying the signature.
     * @param string $token
     * @return object|null
     */
    public static function decodeNoVerify(string $token) : ? object {
        
        list($header, $payload, $signature) = explode(".", $token);
        
        $decoded = json_decode(Webservice::base64UrlDecode($payload));
        
        if ($decoded) {
            return $decoded;
        }
        
        return null;
    }
    
    /**
     * Is the token a JWT?
     * @param string $token
     * @return bool
     */
    public static function isJWT(string $token) : bool {
        
        list($header, $payload, $signature) = explode(".", $token);
    
        if (empty($header) || !json_decode(Webservice::base64UrlDecode($header))) {
          return false;
        }

        if (empty($payload) || !json_decode(Webservice::base64UrlDecode($payload))) {
          return false;
        }

        if (empty($signature)) {
          return false;
        }

        return true;
    }
}