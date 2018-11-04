<?php

/**
 * Geolocation callback
 */

namespace IdnoPlugins\Checkin\Pages {

    /**
     * Default class to serve the geolocation callback
     */
    class Geocoder extends \Idno\Common\Page
    {

        function post()
        {

            $this->gatekeeper(); // Logged-in users only

            $geocoder = new \Idno\Core\Geocoder();

            $lat = $this->getInput('lat');
            $long = $this->getInput('long');
            if (!empty($lat) && (!empty($long))) {
                echo json_encode($geocoder->queryLatLong($lat, $long), JSON_PRETTY_PRINT);
                exit;
            }

            $address = $this->getInput('address');
            if (!empty($address)) {
                echo json_encode($geocoder->queryAddress($address), JSON_PRETTY_PRINT);
                exit;
            }
        }

    }

}

