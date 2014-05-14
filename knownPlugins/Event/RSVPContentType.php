<?php

    namespace knownPlugins\Event {

        class RSVPContentType extends \known\Common\ContentType {

            public $title = 'RSVP';
            public $entity_class = 'knownPlugins\\Event\\RSVP';
            public $logo = '<i class="icon-calendar"></i>';
            public $indieWebContentType = ['rsvp'];

        }

    }