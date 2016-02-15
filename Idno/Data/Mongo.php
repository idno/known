<?php

/**
 * MongoDB back-end for Known data.
 *
 * This is a wrapper for DataConcierge, but begins to move mongo specific settings
 * to its own class.
 * 
 * @package idno
 * @subpackage data
 */

namespace Idno\Data {

    use Idno\Core\Idno;

    class Mongo extends \Idno\Core\DataConcierge {
        
        private $dbstring;
        private $dbauthsrc;
        private $dbname;
        private $dbuser;
        private $dbpass;

        function __construct($dbstring = null, $dbuser = null, $dbpass = null, $dbname = null, $dbauthsrc = null) {

            $this->dbstring = $dbstring;
            $this->dbuser = $dbuser;
            $this->dbpass = $dbpass;
            $this->dbname = $dbname;
            $this->dbauthsrc = $dbauthsrc;

            if (empty($dbstring)) {
                $this->dbstring = \Idno\Core\Idno::site()->config()->dbstring;
            }
            if (empty($dbuser)) {
                $this->dbuser = \Idno\Core\Idno::site()->config()->dbuser;
            }
            if (empty($dbpass)) {
                $this->dbpass = \Idno\Core\Idno::site()->config()->dbpass;
            }
            if (empty($dbname)) {
                $this->dbname = \Idno\Core\Idno::site()->config()->dbname;
            }
            if (empty($dbauthsrc)) {
                $this->dbauthsrc = \Idno\Core\Idno::site()->config()->dbauthsrc;
            }
            parent::__construct();
        }

        function init() {
            try {
                $this->client = new \MongoClient($this->dbstring, array_filter([
                            'authSource' => $this->dbauthsrc,
                            'username' => $this->dbuser,
                            'password' => $this->dbpass,
                ]));
            } catch (\MongoConnectionException $e) {
                http_response_code(500);
                echo '<p>Unfortunately we couldn\'t connect to the database:</p><p>' . $e->getMessage() . '</p>';
                exit;
            }

            $this->database = $this->client->selectDB($this->dbname);
        }

    }

}

