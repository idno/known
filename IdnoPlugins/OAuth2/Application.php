<?php

namespace IdnoPlugins\OAuth2 {


    class Application extends \Idno\Common\Entity
    {

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

            return $app;
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

    }

}