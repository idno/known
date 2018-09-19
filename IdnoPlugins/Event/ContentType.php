<?php

    namespace IdnoPlugins\Event {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Event';
            public $category_title = 'Events';
            public $entity_class = 'IdnoPlugins\\Event\\Event';
            public $logo = '<i class="icon-calendar"></i>';
            public $indieWebContentType = array('event');

        }

    }
    