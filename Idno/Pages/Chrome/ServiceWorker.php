<?php

    namespace Idno\Pages\Chrome {

        use Idno\Common\Page;

        /**
         * Default service worker.
         * 
         * Explaination for doing it this way: 
         * 1) Service workers need to be in the top level of the app, which makes things untidy if this is a physical file.
         * 2) Service workers are very application specific, and I'm only writing a stub for now, so doing it as a virtual page allows plugins to provide their own.
         */
        class ServiceWorker extends Page
        {

            function getContent()
            {
                
                header('Content-Type: application/javascript');
                if (!empty($this->arguments[0])) {
                    echo file_get_contents(\Idno\Core\Idno::site()->config()->path . '/js/service-worker.min.js');
                } else {
                    echo file_get_contents(\Idno\Core\Idno::site()->config()->path . '/js/service-worker.js');
                }
                
            }

        }

    }
    