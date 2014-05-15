<?php

    namespace knownPlugins\Event {

        class ContentType extends \known\Common\ContentType {

            public $title = 'Event';
            public $category_title = 'Events';
            public $entity_class = 'knownPlugins\\Event\\Event';
            public $logo = '<i class="icon-calendar"></i>';

        }

    }