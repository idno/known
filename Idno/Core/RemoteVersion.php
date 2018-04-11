<?php


namespace Idno\Core {

    /**
     * Retrieve the remote version details from the project's home on GitHub
     */
    class RemoteVersion extends \Idno\Core\Version {
        
        private static $remoteDetails = [];
        private static $remoteVersion = 'https://raw.githubusercontent.com/idno/Known/master/version.known';
       
        protected static function parse() {

            if (!empty(static::$details))
                return static::$details;

            try {
                $versionfile = Webservice::get(static::$remoteVersion);

                if (!empty($versionfile)) {
                
                    static::$remoteDetails = @parse_ini_string($versionfile['content']);

                    return static::$remoteDetails;    
                }
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->logging()->error($e->getMessage());
            }
            
            return [];
        }
        
    }
}