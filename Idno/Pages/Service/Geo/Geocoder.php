<?php

/**
 * Geolocation callback
 */

namespace Idno\Pages\Service\Geo {

    /**
     * Default class to serve the geolocation callback
     */
    class Geocoder extends \Idno\Common\Page
    {

        function getContent()
        {

            $this->gatekeeper(); // Logged-in users only

            $geocoder = new \Idno\Core\Geocoder();

            $lat = $this->getInput('lat');
            $long = $this->getInput('long');
            if (!empty($lat) && (!empty($long))) {
                \Idno\Core\Idno::site()->response()->setJsonContent(json_encode($geocoder->queryLatLong($lat, $long), JSON_PRETTY_PRINT));
                \Idno\Core\Idno::site()->sendResponse();
            }

            $address = $this->getInput('address');
            if (!empty($address)) {
                \Idno\Core\Idno::site()->response()->setJsonContent(json_encode($geocoder->queryAddress($address), JSON_PRETTY_PRINT));
                \Idno\Core\Idno::site()->sendResponse();
            }
        }

    }

}

