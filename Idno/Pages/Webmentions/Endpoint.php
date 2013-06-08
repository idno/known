<?php

    /**
     * Webmentions endpoint
     */

    namespace Idno\Pages\Webmentions {

        /**
         * Class to serve the webmention endpoint
         */
        class Endpoint extends \Idno\Common\Page
        {

            function getContent() {
                echo ':)';
            }

            function postContent() {
                $source = $this->getInput('source');
                $target = $this->getInput('target');
                $this->setResponse(202);    // Webmention received a-ok.
            }

        }

    }