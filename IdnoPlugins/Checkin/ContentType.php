<?php

    namespace IdnoPlugins\Checkin {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Location';
            public $category_title = 'Locations';
            public $entity_class = 'IdnoPlugins\\Checkin\\Checkin';
            public $indieWebContentType = array('checkin');

        }

    }
