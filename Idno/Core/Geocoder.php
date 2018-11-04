<?php

/**
 * Geocoding tools.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    /**
     * Default Geocoder tool.
     */
    class Geocoder extends \Idno\Common\Component
    {

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Get the endpoint for the geocoder.
         * @return string
         */
        protected function getEndpoint()
        {
            return 'https://nominatim.openstreetmap.org/';
        }

        /**
         * Given a latitude and longitude, reverse geocode it into a structure including name, address,
         * city, etc
         *
         * @param $latitude
         * @param $longitude
         * @return bool|mixed
         */
        static function queryLatLong($latitude, $longitude)
        {

            $query    = $this->getEndpoint() . "reverse?lat={$latitude}&lon={$longitude}&format=json&zoom=18";
            $response = array();

            $http_response = \Idno\Core\Webservice::get($query)['content'];

            if (!empty($http_response)) {
                if ($contents = @json_decode($http_response)) {
                    if (!empty($contents->address)) {
                        $addr             = (array)$contents->address;
                        $response['name'] = implode(', ', array_slice($addr, 0, 1));
                    }
                    if (!empty($contents->display_name)) {
                        $response['display_name'] = $contents->display_name;
                    }

                    return $response;
                }
            }

            return false;

        }

        /**
         * Takes an address and returns OpenStreetMap data via Nominatim, including latitude and longitude
         *
         * @param string $address
         * @return array|bool
         */
        function queryAddress($address)
        {

            $query = $this->getEndpoint() . "search?q=" . urlencode($address) . "&format=json";

            $http_response = \Idno\Core\Webservice::get($query)['content'];

            if (!empty($http_response)) {
                if ($contents = @json_decode($http_response)) {
                    $contents              = (array)$contents;
                    $contents              = (array)array_pop($contents); // This will have been an array wrapped in an array
                    $contents['latitude']  = $contents['lat'];
                    $contents['longitude'] = $contents['lon'];

                    return $contents;
                }
            }

            return false;

        }

    }

}