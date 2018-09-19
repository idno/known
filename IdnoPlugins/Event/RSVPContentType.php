<?php

    namespace IdnoPlugins\Event {

        class RSVPContentType extends \Idno\Common\ContentType {

            public $title = 'RSVP';
            public $entity_class = 'IdnoPlugins\\Event\\RSVP';
            public $logo = '<i class="icon-calendar"></i>';
            public $indieWebContentType = array('rsvp');

        }

    }
    