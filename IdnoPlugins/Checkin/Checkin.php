<?php

namespace IdnoPlugins\Checkin {

    class Checkin extends \Idno\Common\Entity
        implements \Idno\Common\JSONLDSerialisable
    {

        function getTitle()
        {
            return \Idno\Core\Idno::site()->language->_('Checked into %s', [$this->placename]);
        }

        function getDescription()
        {
            if (empty($this->body)) {
                return ' ';
            }
            return $this->body;
        }

        function getMetadataForFeed()
        {
            return array(
                'type' => 'checkin',
                'latitude' => $this->lat,
                'longitude' => $this->long,
                'placename' => $this->placename,
                'address' => $this->address
            );
        }

        /**
         * Status objects have type 'note'
         * @return 'note'
         */
        function getActivityStreamsObjectType()
        {
            return 'place';
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
            $body         = \Idno\Core\Idno::site()->currentPage()->getInput('body');
            $tags         = \Idno\Core\Idno::site()->currentPage()->getInput('tags');
            $lat          = \Idno\Core\Idno::site()->currentPage()->getInput('lat');
            $long         = \Idno\Core\Idno::site()->currentPage()->getInput('long');
            $user_address = \Idno\Core\Idno::site()->currentPage()->getInput('user_address');
            $placename    = \Idno\Core\Idno::site()->currentPage()->getInput('placename');
            $tags         = \Idno\Core\Idno::site()->currentPage()->getInput('tags');
            $access       = \Idno\Core\Idno::site()->currentPage()->getInput('access');

            if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                if ($time = strtotime($time)) {
                    $this->created = $time;
                }
            }

            if (!empty($lat) && !empty($long)) {
                $this->lat       = $lat;
                $this->long      = $long;
                $this->placename = $placename;
                $this->title     = \Idno\Core\Idno::site()->language()->_('Checked into %s', [$placename]);
                $this->body      = $body;
                $this->address   = $user_address;
                $this->setAccess($access);
                $this->tags = $tags;
                if ($this->publish($new)) {
                    if ($new && $access == 'PUBLIC') {
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                    }

                    return true;
                }
            } else {
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('You can\'t save an empty checkin.'));
            }

            return false;

        }

        function deleteData()
        {
            if ($this->getAccess() == 'PUBLIC') {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
            }
        }

        public function jsonLDSerialise(array $params = array()): array
        {

            $json = [
                "@context" => "http://schema.org",
                "@type" => "CheckInAction",
                'agent' => [
                    "@type" => "Person",
                    "name" => $this->getOwner()->getName()
                ],
                'location' => [
                    '@type' => 'Place',
                    'address' => $this->address,
                    'name' => $this->placename,
                    "geo" => [
                        "@type" => "GeoCoordinates",
                        "latitude" => $this->lat,
                        "longitude" => $this->long
                    ],
                ],
            ];

            return $json;
        }

    }

}
