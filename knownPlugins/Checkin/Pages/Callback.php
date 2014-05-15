<?php

    /**
     * Geolocation callback
     */

    namespace knownPlugins\Checkin\Pages {

        /**
         * Default class to serve the geolocation callback
         */
        class Callback extends \known\Common\Page
        {

            function post()
            {
                $this->gatekeeper(); // Logged-in users only
                $lat = $this->getInput('lat');
                $long = $this->getInput('long');
                if (!empty($lat) && (!empty($long))) {
                    echo json_encode(\knownPlugins\Checkin\Checkin::queryLatLong($lat, $long));
                }
            }

        }

    }